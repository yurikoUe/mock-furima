@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>住所の変更</h1>

    <form method="POST" action="{{ route('address.update', ['item_id' => $product->id]) }}" class="form">
        @csrf
        <div class="form__group">
            <label for="order_postal_code" class="form__label">郵便番号</label>
            <input class="form__input" type="text" id="order_postal_code" name="order_postal_code" value="{{ old('order_postal_code') }}" required>
        </div>
        <div class="form__group">
            <label for="order_address" class="form__label">住所</label>
            <input class="form__input" type="text" id="order_address" name="order_address" value="{{ old('order_address') }}" required>
        </div>
        <div class="form__group">
            <label for="order_building" class="form__label">建物名</label>
            <input class="form__input" type="text" id="order_building" name="order_building" value="{{ old('order_building') }}">
        </div>
        <button type="submit" class="submit-btn">更新する</button>
    </form>
</div>
@endsection
