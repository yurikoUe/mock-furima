@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login-register-profile.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>住所の変更</h1>

    

    <form method="POST" action="{{ route('address.save', ['item_id' => $product->id]) }}">
        @csrf
        <div class="form__group">
            <label for="order_postal_code">郵便番号</label>
            <input type="text" id="order_postal_code" name="order_postal_code" value="{{ old('order_postal_code') }}" required>
        </div>
        <div class="form__group">
            <label for="order_address">住所</label>
            <input type="text" id="order_address" name="order_address" value="{{ old('order_address') }}" required>
        </div>
        <div class="form__group">
            <label for="order_building">建物名</label>
            <input type="text" id="order_building" name="order_building" value="{{ old('order_building') }}">
        </div>
        <button type="submit">更新する</button>
        <!-- order_addressesテーブルに保存 -->
    </form>
</div>
@endsection
