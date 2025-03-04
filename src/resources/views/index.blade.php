@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/alert.css') }}">

@endsection

@section('content')
<div class="alert">
    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert--error">
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div>
            {{ session('info') }}
        </div>
    @endif

    @if (session('cancel'))
        <div>
            {{ session('cancel') }}
        </div>
    @endif

    @if (session('purchase'))
        <div class="alert alert-warning">
            {{ session('purchase') }}
        </div>
    @endif


    @if (session('cancel'))
        <div class="alert alert-warning">
            {{ session('cancel') }}
        </div>
    @endif
</div>
<div>
    <!-- タブ -->
    <div class="tabs">
        <a href="{{ route('index', ['tab' => 'recommend', 'keyword' => request()->get('keyword')]) }}"
        class="tabs__link {{ request()->get('tab') != 'mylist' ? 'tabs__link--active' : '' }}">おすすめ</a>

        <a href="{{ route('index', ['tab' => 'mylist', 'keyword' => request()->get('keyword')]) }}"
        class="tabs__link {{ request()->get('tab') == 'mylist' ? 'tabs__link--active' : '' }}">マイリスト</a>
    </div>

    <!-- 商品一覧表示 -->
    <div class="product-list">
        @foreach ($products as $product)
            @if (auth()->check() && auth()->user()->id !== $product->user_id) <!-- 自分が出品した商品は非表示 -->
                <div class="product-list__item">
                    <!-- 商品詳細ページに遷移 -->
                    <a href="{{ route('product.show', ['item_id' => $product->id]) }}">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        <p class="product-list__name">{{ $product->name }}</p>
                    </a>
                    @if ($product->isSold ?? false)
                        <span class="product-list__sold">SOLD</span>
                    @endif
                </div>
            @elseif (!auth()->check())
                <div class="product-list__item">
                    <!-- 商品詳細ページに遷移 -->
                    <a href="{{ route('product.show', ['item_id' => $product->id]) }}">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        <p class="product-list__name">{{ $product->name }}</p>
                    </a>
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
