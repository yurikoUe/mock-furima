@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div>
    <!-- タブ -->
    <div class="tabs">
        <a href="{{ route('index') }}" class="tabs__link {{ request()->get('tab') != 'mylist' ? 'tabs__link--active' : '' }}">おすすめ</a>
        <a href="{{ route('index', ['tab' => 'mylist']) }}" class="tabs__link {{ request()->get('tab') == 'mylist' ? 'tabs__link--active' : '' }}">マイリスト</a>
    </div>

    <!-- 商品一覧表示 -->
    <div class="product-list">
        @foreach ($products as $product)
            @if (auth()->check() && auth()->user()->id !== $product->user_id) <!-- 自分が出品した商品は非表示 -->
                <div class="product-list__item">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    <p class="product-list__name">{{ $product->name }}</p>
                
                    @php
                        $sold = \App\Models\Order::where('product_id', $product->id)->exists();//商品が注文されているか
                    @endphp
                    @if ($sold)
                        <span class="product-list__sold">Sold</span>
                    @endif
                </div>
            @elseif (!auth()->check())
                <div class="product-list__item">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    <p class="product-list__name">{{ $product->name }}</p>
                    @php
                        $sold = \App\Models\Order::where('product_id', $product->id)->exists();//商品が注文されているか
                    @endphp
                    @if ($sold)
                        <span class="product-list__sold">Sold</span>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</div>

@endsection
