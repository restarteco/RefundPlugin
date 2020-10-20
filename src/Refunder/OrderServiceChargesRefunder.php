<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\ServiceChargeRefunded;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ServiceChargeRefund;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderServiceChargesRefunder implements RefunderInterface
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(RefundCreatorInterface $refundCreator, MessageBusInterface $eventBus)
    {
        $this->refundCreator = $refundCreator;
        $this->eventBus = $eventBus;
    }

    public function refundFromOrder(array $units, string $orderNumber): int
    {
        $refundedTotal = 0;

        /** @var ServiceChargeRefund $serviceChargeUnit */
        foreach ($units as $serviceChargeUnit) {
            $this->refundCreator->__invoke($orderNumber, $serviceChargeUnit->id(), $serviceChargeUnit->total(), RefundType::serviceCharge());

            $refundedTotal += $serviceChargeUnit->total();

            $this->eventBus->dispatch(new ServiceChargeRefunded($orderNumber, $serviceChargeUnit->id(), $serviceChargeUnit->total()));
        }

        return $refundedTotal;
    }
}
