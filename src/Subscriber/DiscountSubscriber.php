<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\ShopGo\Subscriber;

use Lyrasoft\ShopGo\Event\AfterComputeTotalsEvent;
use Lyrasoft\ShopGo\Event\BeforeComputeTotalsEvent;
use Lyrasoft\ShopGo\Event\ComputingTotalsEvent;
use Lyrasoft\ShopGo\Event\PrepareProductPricesEvent;
use Lyrasoft\ShopGo\Service\DiscountService;
use Windwalker\Event\Attributes\EventSubscriber;
use Windwalker\Event\Attributes\ListenTo;

/**
 * The DiscountSubscriber class.
 */
#[EventSubscriber]
class DiscountSubscriber
{
    public function __construct(protected DiscountService $discountService)
    {
    }

    #[ListenTo(BeforeComputeTotalsEvent::class)]
    public function beforeComputeTotals(BeforeComputeTotalsEvent $event): void
    {
        //
    }

    #[ListenTo(ComputingTotalsEvent::class)]
    public function computeTotals(ComputingTotalsEvent $event): void
    {
        $this->discountService->computeGlobalDiscounts($event);
    }

    #[ListenTo(PrepareProductPricesEvent::class)]
    public function prepareProductPrices(PrepareProductPricesEvent $event): void
    {
        $context = $event->getContext();

        $this->discountService->computeSingleProductSpecials($event);

        if ($context === $event::CART || $context === $event::ORDER) {
            $cartItem = $event->getCartItem();

            $priceSet = $this->discountService->computeSingleProductDiscounts($event, $cartItem->getQuantity())
                ->getPricing();

            $event->setPricing($priceSet);
        }

        // $this->discountService->computeGlobalDiscountsForProduct($event);
    }
}
