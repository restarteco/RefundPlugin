<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Converter;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Webmozart\Assert\Assert;

final class ServiceChargeLineItemsConverter implements LineItemsConverterInterface
{
    /** @var RepositoryInterface */
    private $adjustmentRepository;

    public function __construct(RepositoryInterface $adjustmentRepository)
    {
        $this->adjustmentRepository = $adjustmentRepository;
    }

    public function convert(array $units): array
    {
        $lineItems = [];

        /** @var UnitRefundInterface $unitRefund */
        foreach ($units as $unitRefund) {
            $lineItems[] = $this->convertUnitRefundToLineItem($unitRefund);
        }

        return $lineItems;
    }

    private function convertUnitRefundToLineItem(UnitRefundInterface $unitRefund): LineItemInterface
    {
        /** @var AdjustmentInterface|null $serviceChargeAdjustment */
        $serviceChargeAdjustment = $this
            ->adjustmentRepository
            ->findOneBy(['id' => $unitRefund->id(), 'originCode' => 'service_charge'])
        ;
        Assert::notNull($serviceChargeAdjustment);
        Assert::lessThanEq($unitRefund->total(), $serviceChargeAdjustment->getAmount());

        return new LineItem(
            $serviceChargeAdjustment->getLabel(),
            1,
            $unitRefund->total(),
            $unitRefund->total(),
            $unitRefund->total(),
            $unitRefund->total(),
            0
        );
    }
}
