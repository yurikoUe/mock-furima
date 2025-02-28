@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="product-purchase">
    <!-- コンテンツ左側 -->
    <div class="product-purchase__left">
        <div class="product-purchase__product">
            <img class="product-purchase__image" src="{{ asset('storage/' . $product->image) }}" alt="商品画像">
            <div class="product-purchase__info">
                <h1>{{ $product->name }}</h1>
                <p class="product-purchase__price">¥ {{ $product->price }}</p>
            </div>
        </div>
        <div class="product-purchase__payment">
            <h2>支払い方法</h2>
            <select class="product-purchase__payment-select" name="payment_method" id="payment_method" onchange="updatePaymentMethod()">
                <option value="" disabled selected>選択してください</option>
                @foreach ($paymentMethods as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="product-purchase__address">
            <div class="product-purchase__address-header">
                <h2>配送先</h2>
                <a class="product-purchase__address-change" href="{{ route('address.change',  ['item_id' => $product->id]) }}">変更する</a>
            </div>
            <table class="product-purchase__address-table">
                <tr>
                    <td><span>〒</span>{{ $orderAddress->order_postal_code }}</td>
                </tr>
                <tr>
                    <td>{{ $orderAddress->order_address }}{{ $orderAddress->order_building }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- コンテンツ右側 -->
    <div class="product-purchase__right">
        <!-- @if (session('success'))
            <div class="product-purchase__message product-purchase__message--success">
                {{ session('success') }}
            </div>
        @endif -->

        <table class="product-purchase__summary">
            <tr>
                <td>商品代金</td>
                <td>¥ {{ $product->price }}</td>
            </tr>
            <tr>
                <td>支払い方法</td>
                <td>
                    <select class="product-purchase__summary-payment" name="selected_payment_method" id="selected_payment_method" disabled>
                        @foreach ($paymentMethods as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
        </table>

        @if ($isSold)
            <p class="product-purchase__status product-purchase__status--sold">SOLD</p>
        @else
            <form class="product-purchase__form" method="POST" action="{{ route('order.store',  ['item_id' => $product->id]) }}">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="payment_method" id="payment_method_input">
                <input type="hidden" name="order_postal_code" value="{{ $orderAddress->order_postal_code }}">
                <input type="hidden" name="order_address" value="{{ $orderAddress->order_address }}">
                <input type="hidden" name="order_building" value="{{ $orderAddress->order_building }}">
                <button class="product-purchase__button" type="submit">購入する</button>
            </form>
        @endif
    </div>
</div>

<script>
    function updatePaymentMethod() {
        let selectedMethod = document.getElementById('payment_method').value;
        document.getElementById('selected_payment_method').value = selectedMethod;
        document.getElementById('payment_method_input').value = selectedMethod;
    }

    window.onload = function() {
        updatePaymentMethod();
    };
</script>
@endsection
