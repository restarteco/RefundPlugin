<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Model;

final class ServiceChargeRefund implements UnitRefundInterface
{
    /** @var int */
    private $adjusmentId;

    /** @var int */
    private $total;

    public function __construct(int $adjusmentId, int $total)
    {
        $this->adjusmentId = $adjusmentId;
        $this->total = $total;
    }

    public function id(): int
    {
        return $this->adjusmentId;
    }

    public function total(): int
    {
        return $this->total;
    }
}
