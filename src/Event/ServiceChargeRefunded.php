<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Event;

final class ServiceChargeRefunded
{
    /** @var string */
    private $orderNumber;

    /** @var int */
    private $serviceChargeId;

    /** @var int */
    private $amount;

    public function __construct(string $orderNumber, int $serviceChargeId, int $amount)
    {
        $this->orderNumber = $orderNumber;
        $this->serviceChargeId = $serviceChargeId;
        $this->amount = $amount;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function serviceChargeId(): int
    {
        return $this->serviceChargeId;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
