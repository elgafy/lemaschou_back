<?php

namespace App\Services\Payments\DTOs;

readonly class PaymentResult
{
    public function __construct(
        public string $status,
        public ?string $transactionId = null,
        public ?string $orderId = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $rrn = null,
        public ?array $rawResponse = null,
    ) {}

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
