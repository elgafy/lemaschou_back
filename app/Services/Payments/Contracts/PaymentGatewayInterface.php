<?php

namespace App\Services\Payments\Contracts;

use App\Models\Order;
use App\Services\Payments\DTOs\PaymentResult;

interface PaymentGatewayInterface
{
    /**
     * Initiate a checkout session with the payment gateway.
     *
     * @return array{redirect_url: string, gateway_session_id: string}
     */
    public function initiate(Order $order, string $customerName, string $customerEmail, string $customerPhone): array;

    /**
     * Verify a transaction status by its gateway transaction ID.
     */
    public function verify(string $transactionId): PaymentResult;

    /**
     * Process an incoming webhook payload and return normalized result.
     */
    public function handleWebhook(array $payload): PaymentResult;
}
