<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\DTOs\PaymentResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EdfapayGateway implements PaymentGatewayInterface
{
    private string $baseUrl;

    private string $apiKey;

    private string $checkoutUrl;

    public function __construct()
    {
        $this->baseUrl = config('payment.gateways.edfapay.base_url');
        $this->apiKey = config('payment.gateways.edfapay.api_key');
        $this->checkoutUrl = config('payment.gateways.edfapay.checkout_url');
    }

    public function checkoutUrl(string $sessionId): string
    {
        return $this->checkoutUrl.'?sessionId='.urlencode($sessionId);
    }

    public function initiate(Order $order, string $customerName, string $customerEmail, string $customerPhone, string $locale, string $reservationId): array
    {
        $successUrl = $this->buildRedirectUrl(config('payment.success_url'), $locale, $reservationId);
        $failureUrl = $this->buildRedirectUrl(config('payment.failure_url'), $locale, $reservationId);

        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
        ])->post($this->baseUrl.'payment-gateway/initiate', [
            'orderId' => (string) $order->id,
            'currency' => $order->currency ?? 'SAR',
            'amount' => (float) $order->total,
            'customerDetails' => [
                'name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
            'auth' => 'N',
            'successUrl' => $successUrl,
            'failureUrl' => $failureUrl,
        ]);

        if ($response->failed()) {
            Log::error('Edfapay initiate failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Payment gateway returned an error: '.$response->body());
        }

        $data = $response->json('data');
        $redirectUrl = $data['redirectUrl'] ?? null;

        if (! $redirectUrl) {
            throw new \RuntimeException('Payment gateway did not return a redirect URL');
        }

        // Extract session ID from the redirect URL
        $sessionId = null;
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY) ?? '', $queryParams);
        $sessionId = $queryParams['sessionId'] ?? null;

        return [
            'redirect_url' => $redirectUrl,
            'gateway_session_id' => $sessionId,
        ];
    }

    /**
     * Fill the {locale} and {reservation_id} placeholders of a redirect URL template.
     */
    private function buildRedirectUrl(string $template, string $locale, string $reservationId): string
    {
        return str_replace(
            ['{locale}', '{reservation_id}'],
            [$locale, $reservationId],
            $template
        );
    }

    public function verify(string $transactionId): PaymentResult
    {
        // Use the dedicated Transaction Details endpoint
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->get($this->baseUrl.'transactions/'.$transactionId.'/details');

        if ($response->failed()) {
            Log::error('Edfapay verify failed', [
                'transaction_id' => $transactionId,
                'status' => $response->status(),
            ]);

            return new PaymentResult(
                status: 'pending',
                transactionId: $transactionId,
                rawResponse: $response->json(),
            );
        }

        $transaction = $response->json('data.transactionDetails');

        if (! $transaction) {
            return new PaymentResult(
                status: 'pending',
                transactionId: $transactionId,
                rawResponse: $response->json(),
            );
        }

        return new PaymentResult(
            status: $this->mapStatus($transaction['paymentStatus'] ?? $transaction['transactionStatus'] ?? ''),
            transactionId: $transaction['transactionId'] ?? $transactionId,
            orderId: $transaction['orderId'] ?? null,
            amount: isset($transaction['amount']) ? (float) $transaction['amount'] : null,
            currency: $transaction['currencyCode'] ?? null,
            rrn: $transaction['rrn'] ?? null,
            rawResponse: $transaction,
        );
    }

    public function verifyByOrderId(string $orderId): PaymentResult
    {
        // filterTransaction with no params returns recent transactions (default 10)
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->get($this->baseUrl.'transactions/filterTransaction');

        if ($response->failed()) {
            Log::error('Edfapay verify by orderId failed', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return new PaymentResult(
                status: 'pending',
                orderId: $orderId,
                rawResponse: $response->json(),
            );
        }

        $transactions = $response->json('data.content', []);
        $transaction = null;

        foreach ($transactions as $txn) {
            if (($txn['orderId'] ?? '') === $orderId) {
                $transaction = $txn;
                break;
            }
        }

        if (! $transaction) {
            Log::info('Edfapay no transaction found for orderId', [
                'order_id' => $orderId,
                'transactions_checked' => count($transactions),
            ]);

            return new PaymentResult(
                status: 'pending',
                orderId: $orderId,
                rawResponse: $response->json(),
            );
        }

        return new PaymentResult(
            status: $this->mapStatus($transaction['paymentStatus'] ?? $transaction['transactionStatus'] ?? ''),
            transactionId: $transaction['transactionId'] ?? null,
            orderId: $transaction['orderId'] ?? $orderId,
            amount: isset($transaction['amount']) ? (float) $transaction['amount'] : null,
            currency: $transaction['currencyCode'] ?? null,
            rrn: $transaction['rrn'] ?? null,
            rawResponse: $transaction,
        );
    }

    public function handleWebhook(array $payload): PaymentResult
    {
        return new PaymentResult(
            status: $this->mapStatus($payload['status'] ?? ''),
            transactionId: $payload['transactionId'] ?? null,
            orderId: $payload['orderId'] ?? null,
            amount: isset($payload['amount']) ? (float) $payload['amount'] : null,
            currency: $payload['currencyCode'] ?? null,
            rrn: $payload['rrn'] ?? null,
            type: $payload['type'] ?? null,
            rawResponse: $payload,
        );
    }

    /**
     * Map Edfapay statuses to normalized statuses.
     */
    private function mapStatus(string $gatewayStatus): string
    {
        return match (strtolower($gatewayStatus)) {
            'approved' => 'approved',
            'declined', 'failed' => 'declined',
            'pending', 'redirect' => 'pending',
            default => 'pending',
        };
    }
}
