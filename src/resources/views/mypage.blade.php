@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endsection

@section('content')
<h2>マイページだ</h2>
<div>
    <div>
        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
    </div>
    <p>{{ $user->name }}</p>
    <a href="{{ route('profile.update') }}">プロフィールを編集</a>
</div>

<!-- タブメニュー -->
<div class="tab-menu">
    <a href="{{ url('/mypage?tab=sell') }}" class="tab-link {{ request('tab', 'sell') == 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="{{ url('/mypage?tab=buy') }}" class="tab-link {{ request('tab') == 'buy' ? 'active' : '' }}">購入した商品</a>
</div>

<!-- タブのコンテンツ -->
@if(request('tab', 'sell') == 'sell')
    <div id="selling" class="tab-content">
        <h3>出品した商品</h3>
        <div>
            @foreach($sellingProducts as $product)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <p class="product-list__name">{{ $product->name }}</p>
            @endforeach
        </div>
    </div>
@elseif(request('tab') == 'buy')
    <div id="purchased" class="tab-content">
        <h3>購入した商品</h3>
        <div>
            @foreach($purchasedProducts as $order)
                <img src="{{ asset('storage/' . $order->product->image) }}" alt="{{ $order->product->name }}">
                <p class="product-list__name">{{ $order->product->name }}</p>
            @endforeach
        </div>
    </div>
@endif
@endsection