@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div>
<h1>商品一覧画面だよ</h1>
<!-- タブ -->
    <div class="tabs">
        <a href="{{ route('index') }}" class="tab {{ request()->get('tab') != 'mylist' ? 'active' : '' }}">おすすめ</a>
        <a href="{{ route('index', ['tab' => 'mylist']) }}" class="tab {{ request()->get('tab') == 'mylist' ? 'active' : '' }}">マイリスト</a>
    </div>

    <!-- 商品一覧表示 -->
    <div class="product-list">
        @foreach ($products as $product)
        <div>
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            <p>{{ $product->name }}</p>
        </div>
        @endforeach
    </div>
</div>

@endsection
