<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use Lyrasoft\Luna\User\UserService;
use Lyrasoft\ShopGo\Script\ShopGoScript;
use Unicorn\Image\ImagePlaceholder;
use Unicorn\Script\UnicornScript;
use Unicorn\Script\VueScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$imagePlaceholder = $app->service(ImagePlaceholder::class);
$userService = $app->service(UserService::class);

$shopGoScript = $app->service(ShopGoScript::class);
$shopGoScript->vueUtilities();
$shopGoScript->productCart();

$vueScript = $app->service(VueScript::class);
$vueScript->vue();
$vueScript->animate();

$uniScript = $app->service(UnicornScript::class);
$uniScript->data('cart.props', [
    'user' => $userService->isLogin() ? $userService->getUser() : null,
]);
$uniScript->data('image.default', $imagePlaceholder->placeholderSquare());

$uniScript->addRoute('@home');
$uniScript->addRoute('@cart_ajax');
$uniScript->addRoute('@address_ajax');

?>

@extends('global.body')

@section('content')
    <div class="l-cart-page container my-5">
        <cart-app>
            <form id="cart-form" ref="form" action="." method="post">
                <div class="row">
                    <div class="col-lg-8 l-cart-page__content">
                        <header class="d-flex align-items-center justify-content-between mb-4">
                            <h3 class="m-0">購物車</h3>

                            <div>
                                <a href="javascript://"
                                    @click="clearCart">
                                    <i class="fa fa-times"></i>
                                    移除所有商品
                                </a>
                            </div>
                        </header>

                        <div class="l-cart-data">

                            {{-- Cart Items --}}
                            <x-cart-items></x-cart-items>

                            {{-- Addresses --}}
                            <div class="">
                                <address-form type="shipping"
                                    title="收件者地址"
                                    :user="user"
                                    v-model="shippingData"
                                    ref="shippingForm"
                                ></address-form>
                                <address-form type="payment"
                                    title="購買者地址"
                                    :user="user"
                                    :sync-data="shippingData"
                                    v-model="paymentData"
                                    ref="paymentForm"
                                ></address-form>
                            </div>

                            {{-- Shippings --}}
                            <div class="l-shippings mb-4">
                                <h3>貨運方式</h3>

                                <template v-if="shippings.length > 0">
                                    <shipping-item v-for="(shipping, i) of shippings" :key="shipping.id"
                                        :shipping="shipping"
                                        :i="i"
                                        :selected="shippingId === shipping.id"
                                        @selected="shippingId = shipping.id"
                                    >
                                    </shipping-item>
                                </template>
                                <div v-else class="card bg-light">
                                    <div class="card-body py-5 text-center">
                                        <template v-if="shippingData.locationId">
                                            目前沒有合適的貨運方式
                                        </template>
                                        <template v-else>
                                            請先選擇送貨地區
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Payments --}}
                            <div class="l-payments mb-4">
                                <h3>付款方式</h3>

                                <template v-if="payments.length > 0">
                                    <payment-item v-for="(payment, i) of payments" :key="payment.id"
                                        :payment="payment"
                                        :i="i"
                                        :selected="paymentId === payment.id"
                                        @selected="paymentId = payment.id"
                                    >
                                    </payment-item>
                                </template>
                                <div v-else class="card bg-light">
                                    <div class="card-body py-5 text-center">
                                        <template v-if="shippingData.shippingId">
                                            目前沒有合適的付款方式
                                        </template>
                                        <template v-else>
                                            請先選擇貨運方式
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 l-cart-page__sidebar">
                        <div class="l-cart-sidebar position-sticky" style="top: 90px">
                            <div class="card">
                                <div class="card-body l-cart-coupons border-bottom">
                                    <h5>優惠碼</h5>
                                    <div class="d-flex gap-2">
                                        <input type="text" class="form-control" v-model="code" />
                                        <button type="button" class="btn btn-secondary text-nowrap"
                                            style="min-width: 100px"
                                            @click="addCode"
                                            :disabled="code === '' || loading"
                                        >
                                            使用
                                        </button>
                                    </div>

                                    <div v-if="coupons.length" class="list-group list-group-flush mt-4">
                                        <div v-for="coupon of coupons" class="list-group-item border-top d-flex">
                                            <div>
                                                <div>
                                                    <strong>
                                                        @{{ coupon.title }}
                                                    </strong>
                                                </div>
                                                <div class="small text-muted">
                                                    @{{ coupon.code }}
                                                </div>
                                            </div>

                                            <div class="ms-auto">
                                                <a href="javascript://"
                                                    class="link-secondary"
                                                    v-tooltip
                                                    title="取消使用優惠碼"
                                                    @click="removeCode(coupon.id)">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="loaded" class="card-body l-cart-totals text-end">
                                    <div v-if="totals.total" class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100">
                                        <div class="l-cart-total__label">
                                            訂單小計
                                        </div>

                                        <div class="l-cart-total__value">
                                            @{{ $formatPrice(totals.total.price, true) }}
                                        </div>
                                    </div>

                                    <div class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100"
                                        v-for="total of filteredTotals">
                                        <div class="l-cart-total__label d-flex gap-2">
                                            <div>
                                                @{{ total.label }}
                                            </div>
                                            <div v-if="total.params.type === 'coupon' || total.params.subtype === 'code'">
                                                <small>(@{{ total.params.code }})</small>
                                            </div>
                                        </div>

                                        <div class="l-cart-total__value">
                                            @{{ $formatPrice(total.price, true) }}
                                        </div>
                                    </div>

                                    <div v-if="totals.grand_total" class="l-cart-total d-flex justify-content-between gap-1 mb-1 w-100 fs-5 fw-bold">
                                        <div class="l-cart-total__label">
                                            訂單總計
                                        </div>

                                        <div class="l-cart-total__value">
                                            @{{ $formatPrice(totals.grand_total.price, true) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 d-grid gap-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <i class="fa fa-truck"></i>
                                        @{{ selectedShipping?.title || '尚未選擇貨運方式' }}
                                    </div>

                                    <div>
                                        <i class="fa fa-credit-card"></i>
                                        @{{ selectedPayment?.title || '尚未選擇付款方式' }}
                                    </div>
                                </div>

                                <button type="button" class="btn btn-primary btn-lg"
                                    :disabled="loading || !canCheckout"
                                    @click="checkout"
                                >
                                    @{{ loading ? '載入中' : '結帳' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </cart-app>
    </div>

    <x-address-form></x-address-form>
    <x-shipping-item></x-shipping-item>
    <x-payment-item></x-payment-item>
    <x-vue.cascade-select></x-vue.cascade-select>
@stop
