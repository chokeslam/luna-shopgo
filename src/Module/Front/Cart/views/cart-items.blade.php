<?php

declare(strict_types=1);

namespace App\view;

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

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>
<div class="l-cart-items">

    {{-- Cart Item --}}
    <div class="c-cart-item card mb-3" v-for="item of items"
        :data-product-id="item.product.id"
        :data-variant-id="item.variant.id"
    >
        <div class="card-body d-grid d-lg-flex gap-3">
            <div class="d-flex gap-3 me-auto">
                {{-- Checkbox --}}
                <div v-if="partialCheckout" class="c-cart-item__checkbox">
                    <input type="checkbox" class="form-check-input"
                        v-model="item.options.checked"
                        @change="updateChecks"
                    />
                </div>

                {{-- Cover --}}
                <div class="c-cart-item__image">
                    <div style="width: 75px" class="ratio ratio-1x1">
                        <img class="object-fit-cover" :src="item.cover" :alt="item.product.title"
                            style="">
                    </div>
                </div>

                {{-- Content --}}
                <div class="c-cart-item__content">
                    <h5>
                        <a :href="item.link" target="_blank">
                            @{{ item.product.title }}
                        </a>
                    </h5>
                    <div v-if="!item.variant.primary" class="fs-6 text-muted">
                        @{{ item.variant.title }}
                    </div>
                    <div class="text-muted small">
                        @{{ item.product.model }}
                    </div>
                </div>

                <div v-if="item.outOfStock">
                    <span
                        class="badge bg-danger">
                        庫存不足
                    </span>
                </div>
            </div>

            {{-- Quantity --}}
            <div class="c-cart-item__quantity d-flex gap-2">
                <div class="">
                    <div class="input-group flex-nowrap">
                        <button type="button" class="btn btn-secondary btn-sm"
                            @click="changeItemQuantity(item, -1)">
                            <i class="fa fa-minus"></i>
                        </button>
                        <input type="text" class="form-control form-control-sm"
                            v-model.number="item.quantity"
                            @change="updateQuantities(item)"
                            style="width: 75px"
                        />
                        <button type="button" class="btn btn-secondary btn-sm"
                            @click="changeItemQuantity(item, +1)">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3">
                {{-- Item Total --}}
                <div class="c-cart-item__price text-end text-nowrap "
                    style="min-width: 135px">

                    <div v-if="item.priceSet.base_total.price !== item.priceSet.final_total.price"
                        class="small text-muted">
                        <del>@{{ $formatPrice(item.priceSet.base_total.price) }}</del>
                    </div>

                    <div class="fs-5">
                        @{{ $formatPrice(item.priceSet.final_total.price, true) }}
                    </div>
                </div>

                <div class="c-cart-item__actions ms-auto">
                    {{-- Remove --}}
                    <button type="button" class="btn btn-link link-secondary btn-sm"
                        @click="removeItem(item, i)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Attachments --}}
        <div v-if="item.attachments.length > 0" class="card-footer py-4 px-3 px-lg-5">
            <h6>加價購</h6>

            <div v-for="attachment of item.attachments"
                class="c-attachment w-100 d-grid d-lg-flex gap-3 align-items-center py-2 border-bottom"
                :data-product-id="attachment.product.id"
                :data-variant-id="attachment.variant.id"
            >
                <div class="d-flex gap-3 flex-grow-1 align-items-center">

                    {{-- Cover --}}
                    <div class="c-attachment__image">
                        <div style="width: 45px" class="ratio ratio-1x1">
                            <img class="object-fit-cover"
                                :src="attachment.cover" :alt="attachment.product.title"
                                style="">
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="c-attachment__content">
                        <h5 class="fs-6 mb-0">@{{ attachment.product.title }}</h5>
                        <div v-if="!attachment.variant.primary" class="text-muted small">
                            @{{ attachment.variant.title }}
                        </div>
                    </div>

                    <span v-if="attachment.outOfStock"
                        class="badge bg-danger">
                        庫存不足
                    </span>

                    <div class="c-attachment__quantity ms-auto">
                        x@{{ attachment.quantity * item.quantity }}
                    </div>
                </div>

                <div class="d-flex gap-3 ms-auto ms-lg-0">
                    <div class="c-attachment__total d-flex justify-content-end gap-3"
                        style="width: 250px">
                        {{-- Item Total --}}
                        <div class="c-cart-item__price d-flex align-items-center gap-2 text-end text-nowrap"
                            style="min-width: 135px">
                            <div v-if="attachment.priceSet.base_total.price !== attachment.priceSet.final_total.price"
                                class="small text-muted">
                                <del>@{{ $formatPrice(attachment.priceSet.base_total.price) }}</del>
                            </div>

                            <div class="">
                                @{{ $formatPrice(attachment.priceSet.final_total.price) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Total --}}
            <div class="mt-3 text-end fs-5">
                <strong>商品總價</strong>

                <span class="">
                    @{{ $formatPrice(item.priceSet.attached_final_total.price, true) }}
                </span>
            </div>
        </div>
    </div>
</div>
