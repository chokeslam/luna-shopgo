<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\ShopGo\Cart;

use Brick\Math\Exception\MathException;
use Lyrasoft\ShopGo\Cart\Price\PriceObject;
use Lyrasoft\ShopGo\Cart\Price\PriceSet;
use Lyrasoft\ShopGo\Entity\Product;
use Lyrasoft\ShopGo\Entity\ProductVariant;
use Lyrasoft\ShopGo\Event\AfterComputeTotalsEvent;
use Lyrasoft\ShopGo\Event\BeforeComputeTotalsEvent;
use Lyrasoft\ShopGo\Event\ComputingTotalsEvent;
use Lyrasoft\ShopGo\Event\PrepareCartItemEvent;
use Lyrasoft\ShopGo\Event\PrepareProductPricesEvent;
use Lyrasoft\ShopGo\Repository\ProductVariantRepository;
use Lyrasoft\ShopGo\Service\PricingService;
use Lyrasoft\ShopGo\Service\VariantService;
use Lyrasoft\ShopGo\ShopGoPackage;
use Unicorn\Selector\ListSelector;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Navigator;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\TypeCast;

use function Windwalker\collect;

/**
 * The CartService class.
 */
class CartService
{
    use TranslatorTrait;

    public const FOR_UPDATE = 2 << 0;

    public const INCLUDE_SHIPPING = 2 << 1;

    public const INCLUDE_COUPONS = 2 << 2;

    public function __construct(
        protected ApplicationInterface $app,
        protected ShopGoPackage $shopGo,
        protected Navigator $nav,
        protected ORM $orm,
        #[Autowire]
        protected ProductVariantRepository $variantRepository,
        protected VariantService $variantService,
    ) {
        //
    }

    public function getCartData(int $flags = 0): CartData
    {
        $cartItems = $this->getCartItems((bool) ($flags & static::FOR_UPDATE));

        return $this->createCartDataFromItems($cartItems);
    }

    /**
     * @return  array<CartItem>
     *
     * @throws \ReflectionException
     */
    public function getCartItems(bool $forUpdate = false): array
    {
        $cartStorage = $this->app->service(CartStorage::class);

        $items = $cartStorage->getStoredItems();

        $vIds = array_unique(array_column($items, 'variantId'));

        $variants = $this->variantRepository->getCartListSelector()
            ->where('product_variant.id', $vIds)
            ->tapIf(
                $forUpdate,
                fn (ListSelector $selector) => $selector->forUpdate()
            )
            ->all(ProductVariant::class)
            ->keyBy('id');

        $cartItems = [];

        foreach ($items as $k => $storageItem) {
            /** @var ?ProductVariant $variant */
            $variant = $variants[$storageItem['variantId']] ?? null;

            if (!$variant) {
                continue;
            }

            $variant = clone $variant;

            $product = $this->orm->toEntity(Product::class, $variant->product);
            $mainVariant = $this->orm->toEntity(ProductVariant::class, $variant->main_variant);

            $cartItem = new CartItem();
            $cartItem->setVariant($variant);
            $cartItem->setProduct($product);
            $cartItem->setMainVariant($mainVariant);
            $cartItem->setKey((string) $k);
            $cartItem->setCover($variant->main_variant->cover);
            $cartItem->setLink(
                (string) $product->makeLink($this->nav)
            );
            $cartItem->setQuantity((int) $storageItem['quantity']);
            $cartItem->setPayload($storageItem['payload'] ?? []);

            $cartItem->setPriceSet($variant->getPriceSet());

            // @event
            $event = $this->shopGo->emit(
                PrepareCartItemEvent::class,
                compact(
                    'cartItem',
                    'storageItem',
                    'product',
                    'variant',
                    'mainVariant',
                )
            );

            $cartItems[] = $event->getCartItem();
        }

        return $cartItems;
    }

    /**
     * @param  iterable<CartItem>  $cartItems
     *
     * @return CartData
     * @throws MathException
     */
    public function createCartDataFromItems(iterable $cartItems): CartData
    {
        $cartData = new CartData();

        $totals = new PriceSet();
        $total = PriceObject::create('products_total', '0');

        $cartItems = TypeCast::toArray($cartItems);

        foreach ($cartItems as $item) {
            $total = $total->plus($item->getPriceSet()['final_total']);
        }

        $cartData->setItems(collect($cartItems));

        $finalTotal = PriceObject::create(
            'total',
            '0',
            $this->trans('shopgo.order.total.total')
        );

        // @event BeforeComputeTotalsEvent
        $event = $this->shopGo->emit(
            BeforeComputeTotalsEvent::class,
            compact(
                'total',
                'totals',
                'cartData'
            )
        );

        $totals = $event->getTotals();
        $cartData = $event->getCartData();

        // Now we have grand total, we must check discount min price.
        /** @var CartItem $cartItem */
        foreach ($cartItems as $cartItem) {
            $priceSet = $this->variantService->computeProductPriceSet(
                PrepareProductPricesEvent::CART,
                $cartItem->getProduct()->getData(),
                $cartItem->getVariant()->getData(),
                $cartItem->getMainVariant()->getData(),
                $cartItem->getPriceSet(),
                $cartItem,
            );

            $finalTotal = $finalTotal->plus($priceSet['final_total']);
        }

        $total = $finalTotal;

        // @event ComputingTotalsEvent
        $event = $this->shopGo->emit(
            ComputingTotalsEvent::class,
            compact(
                'total',
                'totals',
                'cartData'
            )
        );

        $total = $event->getTotal();
        $totals = $event->getTotals();
        $cartData = $event->getCartData();

        $freeShipping = false;

        foreach ($cartData->getDiscounts() as $discount) {
            $freeShipping = $freeShipping || $discount->isFreeShipping();
        }

        if (!$freeShipping) {
            // todo: Shipping adds here
            // Todo: Add a flat shipping fee for test
            $shippingFee = PriceObject::create('shipping_fee', '200', '運費');
            $totals->set($shippingFee);
        }

        // Calc Grand Totals
        $grandTotal = $total->clone('grand_total', $this->trans('shopgo.order.total.grand.total'));

        foreach ($totals as $tt) {
            $grandTotal = $grandTotal->plus($tt);
        }

        // @event AfterComputeTotalsEvent
        $event = $this->shopGo->emit(
            AfterComputeTotalsEvent::class,
            compact(
                'total',
                'grandTotal',
                'totals',
                'cartData'
            )
        );

        $total = $event->getTotal();
        $totals = $event->getTotals();
        $grandTotal = $event->getGrandTotal();
        $cartData = $event->getCartData();

        $totals->prepend($total);
        $totals->set($grandTotal);

        $cartData->setTotals($totals);

        return $event->getCartData();
    }
}
