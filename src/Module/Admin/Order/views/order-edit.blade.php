<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        \Lyrasoft\ShopGo\Module\Admin\Order\OrderEditView  The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use Lyrasoft\ShopGo\Cart\Price\PriceSet;
use Lyrasoft\ShopGo\Entity\Order;
use Lyrasoft\ShopGo\Entity\OrderItem;
use Lyrasoft\ShopGo\Module\Admin\Order\OrderEditView;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;
use Windwalker\Form\Form;

/**
 * @var Form        $form
 * @var Order       $item
 * @var OrderItem[] $orderItems
 * @var PriceSet    $totals
 */

$alert = $item->getParams()['alert'] ?? [];

$workflow = $app->service(\Lyrasoft\ShopGo\Workflow\OrderStateWorkflow::class);

$alert = $item->getParams()['alert'] ?? [];
?>

@extends('admin.global.body-edit')

@section('toolbar-buttons')
    @include('edit-toolbar')
@stop

@section('content')
    <form name="admin-form" id="admin-form"
        uni-form-validate='{"scroll": true}'
        action="{{ $nav->to('order_edit') }}"
        method="POST" enctype="multipart/form-data">

        <div class="d-flex flex-column gap-4">
            @if ($alert)
                @foreach ($alert as $msg)
                    <div class="alert alert-warning">
                        {{ $msg }}
                    </div>
                @endforeach
            @endif

            <div class="row">
                <div class="col-md-4">
                    <x-order-info.col1 :order="$item"></x-order-info.col1>
                </div>
                <div class="col-md-4">
                    <x-order-info.col2 :order="$item"></x-order-info.col2>
                </div>
                <div class="col-md-4">
                    <x-order-info.col3 :order="$item"></x-order-info.col3>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <x-order-info.payment-data :order="$item"></x-order-info.payment-data>
                </div>
                <div class="col-md-6">
                    <x-order-info.shipping-data :order="$item"></x-order-info.shipping-data>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                @lang('shopgo.order.field.histories')
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="javascript://" class="btn btn-sm btn-info"
                                    style="width: 150px"
                                    data-bs-toggle="modal"
                                    data-bs-target="#state-change-modal"
                                >
                                    @lang('shopgo.order.button.change.state')
                                </a>
                            </div>
                        </div>

                        <x-order-histories :histories="$histories" class="list-group-flush"></x-order-histories>
                    </div>
                </div>
                <div class="col-lg-8">
                    {{-- Order Items--}}
                    <x-order-info.order-items
                        :order="$item"
                        :order-items="$orderItems"
                        :totals="$totals"
                    ></x-order-info.order-items>
                </div>
            </div>
        </div>

        <div class="d-none">
            @if ($idField = $form?->getField('id'))
                <input name="{{ $idField->getInputName() }}" type="hidden" value="{{ $idField->getValue() }}" />
            @endif

            <x-csrf></x-csrf>
        </div>
    </form>

    <x-state-change-modal id="state-change-modal" :order="$item"></x-state-change-modal>
@stop
