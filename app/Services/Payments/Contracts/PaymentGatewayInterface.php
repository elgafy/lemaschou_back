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
    public function initiate(Order $order, string $customerName, string $customerEmail, string $customerPhone, string $locale, string $reservationId): array;

    /**
     * Verify a transaction status by its gateway transaction ID.
     */
    public function verify(string $transactionId): PaymentResult;

    /**
     * Verify a transaction status by order ID.
     */
    public function verifyByOrderId(string $orderId): PaymentResult;

    /**
     * Build the hosted checkout URL for an existing session, so a customer
     * can retry a payment that was never completed.
     */
    public function checkoutUrl(string $sessionId): string;

    /**
     * Process an incoming webhook payload and return normalized result.
     */
    public function handleWebhook(array $payload): PaymentResult;
}
