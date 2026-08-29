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

    public function __construct()
    {
        $this->baseUrl = config('payment.gateways.edfapay.base_url');
        $this->apiKey = config('payment.gateways.edfapay.api_key');
    }

    public function initiate(Order $order, string $customerName, string $customerEmail, string $customerPhone): array
    {
        $reservation = $order->reservation;

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
            'successUrl' => config('payment.success_url').'?order_id='.$order->id,
            'failureUrl' => config('payment.failure_url').'?order_id='.$order->id,
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

    public function verify(string $transactionId): PaymentResult
    {
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
        ])->get($this->baseUrl.'transactions/filterTransaction', [
            'id' => $transactionId,
        ]);

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

        $transaction = $response->json('data.content.0');

        return new PaymentResult(
            status: $this->mapStatus($transaction['transactionStatus'] ?? $transaction['paymentStatus'] ?? ''),
            transactionId: $transaction['transactionId'] ?? $transactionId,
            orderId: $transaction['orderId'] ?? null,
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
