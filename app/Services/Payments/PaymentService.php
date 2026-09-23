<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\DTOs\PaymentResult;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
    ) {}

    /**
     * Initiate a payment for an order.
     * Creates a Payment record and returns it with the gateway redirect URL.
     *
     * @return array{payment: Payment, redirect_url: string}
     */
    public function initiate(Order $order, Reservation $reservation, string $locale = 'en'): array
    {
        $result = $this->gateway->initiate(
            $order,
            $reservation->first_name.' '.$reservation->last_name,
            $reservation->email,
            $reservation->mobile ?? '',
            $locale,
            $reservation->reservation_id,
        );

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => config('payment.gateway'),
            'amount' => $order->total,
            'currency' => $order->currency ?? 'SAR',
            'status' => 'pending',
            'gateway_session_id' => $result['gateway_session_id'],
        ]);

        Log::info('Payment initiated', [
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'gateway' => $payment->gateway,
        ]);

        return [
            'payment' => $payment,
            'redirect_url' => $result['redirect_url'],
        ];
    }

    /**
     * Build the URL a customer can use to retry an unfinished payment, from the
     * gateway session ID of the existing payment. Returns null when there is no
     * session to resume.
     */
    public function getRetryPaymentUrl(Order $order): ?string
    {
        $payment = $order->payments->firstWhere('status', 'pending')
            ?? $order->payments->sortByDesc('id')->first();

        $sessionId = $payment?->gateway_session_id;

        if (! $sessionId) {
            return null;
        }

        return $this->gateway->checkoutUrl($sessionId);
    }

    /**
     * Handle an incoming webhook from the payment gateway.
     *
     * The webhook result is applied first so the status lands as fast as possible
     * (the customer is redirected back before the webhook is even processed), then
     * confirmed with the gateway, which can still correct it.
     */
    public function handleWebhook(array $payload): void
    {
        $result = $this->gateway->handleWebhook($payload);

        Log::info('Payment webhook received', [
            'status' => $result->status,
            'transaction_id' => $result->transactionId,
            'order_id' => $result->orderId,
        ]);

        // Apply what the webhook told us straight away
        $payment = $this->processResult($result);

        // Then confirm the real status with the gateway. It can still correct an
        // earlier decline (declined -> approved), but it cannot undo an approval —
        // only a refund can do that.
        if ($result->transactionId) {
            try {
                $verified = $this->verify($result->transactionId);

                Log::info('Payment status checked after webhook', [
                    'transaction_id' => $result->transactionId,
                    'webhook_status' => $result->status,
                    'checked_status' => $verified->status,
                ]);
            } catch (\Throwable $e) {
                Log::error('Payment status check after webhook failed', [
                    'transaction_id' => $result->transactionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // An intermediate webhook (pending) returns no payment above, so resolve it
        // here to keep the callback in the history
        $payment ??= $this->resolvePayment($result);

        // Keep every callback we receive, even ones that didn't change the status
        if ($payment) {
            $this->recordAttempt($payment, $payload, $result);
        }
    }

    /**
     * Verify a payment by transaction ID (for post-payment checks).
     */
    public function verify(string $transactionId): PaymentResult
    {
        $result = $this->gateway->verify($transactionId);

        $this->processResult($result);

        return $result;
    }

    /**
     * Verify a payment by order ID — fail-safe when webhook didn't arrive.
     */
    public function verifyByOrderId(string $orderId): PaymentResult
    {
        $result = $this->gateway->verifyByOrderId($orderId);

        if ($result->isFinal() || $result->isRefund()) {
            $this->processResult($result);
        }

        return $result;
    }

    /**
     * Find the payment a gateway result belongs to.
     */
    private function resolvePayment(PaymentResult $result): ?Payment
    {
        $payment = Payment::where('gateway_transaction_id', $result->transactionId)->first();

        // If no payment found by transaction ID, try by order ID. A retry may come
        // back with a different transaction ID, so fall back to the latest payment
        // rather than only pending ones.
        if (! $payment && $result->orderId) {
            $order = Order::find((int) $result->orderId);
            if ($order) {
                $payment = $order->payments()->where('status', 'pending')->first()
                    ?? $order->payments()->latest('id')->first();
            }
        }

        return $payment;
    }

    /**
     * Apply a PaymentResult to the Payment and Order records.
     *
     * Returns the matched payment so callers can record the attempt, or null when
     * no payment could be matched.
     */
    private function processResult(PaymentResult $result): ?Payment
    {
        if (! $result->transactionId) {
            Log::warning('Payment webhook with no transaction ID', ['raw' => $result->rawResponse]);

            return null;
        }

        // Skip intermediate statuses (Pending, Redirect) — only process final outcomes
        if (! $result->isFinal() && ! $result->isRefund()) {
            Log::info('Skipping intermediate webhook status', [
                'status' => $result->status,
                'transaction_id' => $result->transactionId,
            ]);

            return null;
        }

        $payment = $this->resolvePayment($result);

        if (! $payment) {
            Log::warning('Payment not found for webhook', [
                'transaction_id' => $result->transactionId,
                'order_id' => $result->orderId,
            ]);

            return null;
        }

        // Idempotency: an approved payment is only ever changed by a refund
        if (! $result->isRefund() && $payment->status === 'approved') {
            Log::info('Webhook ignored — payment already approved', [
                'payment_id' => $payment->id,
                'webhook_status' => $result->status,
            ]);

            return $payment;
        }

        // Ignore a repeat of the status we already recorded, but let a retry
        // succeed after an earlier decline (declined -> approved)
        if (! $result->isRefund() && $payment->status === $result->status) {
            Log::info('Webhook ignored — payment status unchanged', [
                'payment_id' => $payment->id,
                'current_status' => $payment->status,
                'webhook_status' => $result->status,
            ]);

            return $payment;
        }

        // Update payment record
        $payment->gateway_transaction_id = $result->transactionId;
        $payment->gateway_response = $result->rawResponse;
        $payment->status = $result->status;

        if ($result->isApproved()) {
            $payment->paid_at = now();
        }

        $payment->save();

        // Update order status
        $order = $payment->order;
        if ($order) {
            $order->status = match (true) {
                $result->isRefund() => 'refunded',
                $result->isApproved() => 'paid',
                $result->isDeclined() => 'failed',
                default => $order->status,
            };
            $order->save();
        }

        Log::info('Payment processed', [
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'type' => $result->type,
            'order_id' => $order?->id,
            'order_status' => $order?->status,
        ]);

        return $payment;
    }

    /**
     * Append a gateway callback to the payment's attempt history.
     */
    private function recordAttempt(Payment $payment, array $payload, PaymentResult $result): void
    {
        $history = $payment->payment_history ?? [];

        $history[] = [
            'recorded_at' => now()->toIso8601String(),
            'status' => $result->status,
            'transaction_id' => $result->transactionId,
            'payload' => $payload,
        ];

        $payment->payment_history = $history;
        $payment->save();
    }
}
