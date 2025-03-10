@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')

<div class="mypage-header">
    <div class="mypage-header__info">
        <div class="mypage-header__image">
            @if(auth()->user()->profile_image)
                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="プロフィール画像" class="rounded-circle">
            @else
                <img src="{{ asset('storage/images/default-profile-image.png') }}" alt="デフォルトプロフィール画像" class="default-profile">
            @endif
        </div>
        <p class="mypage-header__name">{{ $user->name }}</p>
    </div>
    <a class="mypage-header__ling" href="{{ route('profile.update') }}">プロフィールを編集</a>
</div>

<!-- タブメニュー -->
<div class="tabs">
    <a href="{{ url('/mypage?tab=sell') }}" class="tabs__link {{ request('tab', 'sell') == 'sell' ? 'tabs__link--active' : '' }}">出品した商品</a>
    <a href="{{ url('/mypage?tab=buy') }}" class="tabs__link {{ request('tab') == 'buy' ? 'tabs__link--active' : '' }}">購入した商品</a>
</div>

<!-- タブのコンテンツ -->
<!-- 出品した商品 -->
@if(request('tab', 'sell') == 'sell')
    <div id="selling" class="tab-content">
        @foreach($sellingProducts as $product)
            <div class="tab-content__item">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <p class="product-list__name">{{ $product->name }}</p>
            </div>
        @endforeach
    </div>
<!-- 購入した商品 -->
@elseif(request('tab') == 'buy')
    <div id="purchased" class="tab-content">
        @foreach($purchasedProducts as $order)
            <div class="tab-content__item">
                <img src="{{ asset('storage/' . $order->product->image) }}" alt="{{ $order->product->name }}">
                <p class="product-list__name">{{ $order->product->name }}</p>
            </div>
        @endforeach
    </div>
@endif
@endsection