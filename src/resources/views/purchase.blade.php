@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endsection

@section('content')
<!-- コンテンツ左側 -->
<div>
    <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像">
    <div  style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;">
        <h1>{{ $product->name }}</h1>
        <p>¥{{ $product->price }}</p>
    </div>

    <div style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;">
        <h2>支払い方法</h2>
        <select name="payment_method" id="payment_method" onchange="updatePaymentMethod()">
            @foreach ($paymentMethods as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div style="border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 20px;">
        <h2>配送先</h2>

        <table>
            <tr>
                <th>郵便番号</th>
                <td>{{ $orderAddress->order_postal_code }}</td>
            </tr>
            <tr>
                <th>住所</th>
                <td>{{ $orderAddress->order_address }}</td>
            </tr>
            <tr>
                <th>建物名</th>
                <td>{{ $orderAddress->order_building }}</td>
            </tr>
        </table>
        <a href="{{ route('address.change',  ['item_id' => $product->id]) }}">配送先を変更するときはこちらをクリック</a>
    </div>
</div>
<!-- コンテンツ右側 -->
<div>

    <!-- 成功メッセージの表示 -->
    @if (session('success'))
        <div style="color: green; font-weight: bold;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <tr>
            <th>商品代金</th>
            <td>{{ $product->price }}円</td>
        </tr>
        <tr>
            <th>支払い方法</th>
            <!-- コンテンツ左側で選択された値を即時表示 -->
            <td>
                <select name="selected_payment_method" id="selected_payment_method">
                    @foreach ($paymentMethods as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </td>
        </tr>
    </table>

    @if ($isSold)
    <p style="color: red; font-weight: bold;">SOLD</p>
    @else
        <form method="POST" action="{{ route('order.store',  ['item_id' => $product->id]) }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="payment_method" id="payment_method_input">
            <input type="hidden" name="order_postal_code" value="{{ $orderAddress->order_postal_code }}">
            <input type="hidden" name="order_address" value="{{ $orderAddress->order_address }}">
            <input type="hidden" name="order_building" value="{{ $orderAddress->order_building }}">
            <button type="submit">購入する</button>
        </form>
    @endif
</div>

<script>
    function updatePaymentMethod() {
        // 左側で選ばれた支払い方法を取得
        let selectedMethod = document.getElementById('payment_method').value;
        // 右側のセレクトボックスに選ばれた値を反映
        document.getElementById('selected_payment_method').value = selectedMethod;
        // フォームに隠しフィールドがある場合、その値も設定
        document.getElementById('payment_method_input').value = selectedMethod;
    }

    window.onload = function() {
        updatePaymentMethod();  // ページロード時に初期値を反映
    };
</script>
@endsection
