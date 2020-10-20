<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Webmozart\Assert\Assert;

final class RemainingTotalProvider implements RemainingTotalProviderInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    /** @var RepositoryInterface */
    private $adjustmentRepository;

    /** @var RepositoryInterface */
    private $refundRepository;

    public function __construct(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository
    ) {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
        $this->adjustmentRepository = $adjustmentRepository;
        $this->refundRepository = $refundRepository;
    }

    public function getTotalLeftToRefund(int $id, RefundType $type): int
    {
        $unitTotal = $this->getRefundUnitTotal($id, $type);
        $refunds = $this->refundRepository->findBy(['refundedUnitId' => $id, 'type' => $type]);

        if (count($refunds) === 0) {
            return $unitTotal;
        }

        $refundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $refundedTotal += $refund->getAmount();
        }

        return $unitTotal - $refundedTotal;
    }

    private function getRefundUnitTotal(int $id, RefundType $refundType): int
    {
        if ($refundType->equals(RefundType::orderItemUnit())) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitRepository->find($id);
            Assert::notNull($orderItemUnit);

            return $orderItemUnit->getTotal();
        }

        if ($refundType->equals(RefundType::shipment())) {
            /** @var AdjustmentInterface $shipment */
            $shipment = $this->adjustmentRepository->findOneBy([
                'id' => $id,
                'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT,
            ]);
            Assert::notNull($shipment);
    
            return $shipment->getAmount();
        }

        /** @var AdjustmentInterface $shipment */
        $serviceCharge = $this->adjustmentRepository->findOneBy([
            'id' => $id,
            'originCode' => 'service_charge',
        ]);
        Assert::notNull($serviceCharge);

        return $serviceCharge->getAmount();
    }
}
