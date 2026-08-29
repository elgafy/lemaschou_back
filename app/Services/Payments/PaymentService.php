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
    public function initiate(Order $order, Reservation $reservation): array
    {
        $result = $this->gateway->initiate(
            $order,
            $reservation->first_name.' '.$reservation->last_name,
            $reservation->email,
            $reservation->mobile ?? '',
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
     * Handle an incoming webhook from the payment gateway.
     */
    public function handleWebhook(array $payload): void
    {
        $result = $this->gateway->handleWebhook($payload);

        Log::info('Payment webhook received', [
            'status' => $result->status,
            'transaction_id' => $result->transactionId,
            'order_id' => $result->orderId,
        ]);

        $this->processResult($result);
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
     * Apply a PaymentResult to the Payment and Order records.
     */
    private function processResult(PaymentResult $result): void
    {
        if (! $result->transactionId) {
            Log::warning('Payment webhook with no transaction ID', ['raw' => $result->rawResponse]);

            return;
        }

        $payment = Payment::where('gateway_transaction_id', $result->transactionId)->first();

        // If no payment found by transaction ID, try by order ID
        if (! $payment && $result->orderId) {
            $order = Order::find($result->orderId);
            if ($order) {
                $payment = $order->payments()->where('status', 'pending')->first();
            }
        }

        if (! $payment) {
            Log::warning('Payment not found for webhook', [
                'transaction_id' => $result->transactionId,
                'order_id' => $result->orderId,
            ]);

            return;
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
            $order->status = match ($result->status) {
                'approved' => 'paid',
                'declined' => 'failed',
                default => $order->status,
            };
            $order->save();
        }

        Log::info('Payment processed', [
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'order_id' => $order?->id,
            'order_status' => $order?->status,
        ]);
    }
}
