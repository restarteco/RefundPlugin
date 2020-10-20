<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Listener;

use Sylius\RefundPlugin\Event\ServiceChargeRefunded;
use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;

final class ServiceChargeRefundedEventListener
{
    /** @var OrderPartiallyRefundedStateResolverInterface */
    private $orderPartiallyRefundedStateResolver;

    public function __construct(OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver)
    {
        $this->orderPartiallyRefundedStateResolver = $orderPartiallyRefundedStateResolver;
    }

    public function __invoke(ServiceChargeRefunded $serviceChargeRefunded): void
    {
        $this->orderPartiallyRefundedStateResolver->resolve($serviceChargeRefunded->orderNumber());
    }
}
