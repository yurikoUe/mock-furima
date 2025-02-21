@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endsection

@section('content')
<!-- コンテンツ左側 -->
<div>
    <img src="{{ $product->image }}" alt="商品画像">
    <div>
        <h1>{{ $product->name }}</h1>
        <p>¥{{ $product->price }}</p>
    </div>
    <!-- ボーダー線を入れる -->
    <div>
        <h2>支払い方法</h2>
        <select name="payment_method" id="payment_method">
            @foreach ($paymentMethods as $key => $method)
                <option value="{{ $key }}">{{ $method }}</option>
            @endforeach
        </select>
    </div>
    <!-- ボーダー線を入れる -->
    <div>
        <h2>配送先</h2>
        <p>{{ auth()->user()->postal_code }} {{ auth()->user()->address }} {{ auth()->user()->building }}</p>
        <a href="">配送先を変更するときはこちらをクリック</a>
    </div>
</div>
<!-- コンテンツ右側 -->
<div>
    <table>
        <tr>
            <th>商品代金</th>
            <td>{{ $product->price }}円</td>
        </tr>
        <tr>
            <th>支払い方法</th>
            <td>
                <select name="payment_method">
                    左側のコンテンツで選んだpayment_methodを表示
                </select>
            </td>
        </tr>
    </table>
    <!-- <form method="POST" action="{{ route('order.store') }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="payment_method" id="payment_method_input">
        <button type="submit">購入する</button>
    </form> -->
</div>
@endsection
