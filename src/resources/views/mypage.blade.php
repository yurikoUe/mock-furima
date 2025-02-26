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
    <a href="#" class="tab-link active" data-tab="selling">出品した商品</a>
    <a href="#" class="tab-link" data-tab="purchased">購入した商品</a>
</div>

<!-- タブのコンテンツ -->
<div id="selling" class="tab-content active">
    <h3>出品した商品</h3>
    <div>
        @foreach($sellingProducts as $product)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            <p class="product-list__name">{{ $product->name }}</p>
        @endforeach
    </div>
</div>

<div id="purchased" class="tab-content">
    <h3>購入した商品</h3>
    <div>
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
        <p class="product-list__name">{{ $product->name }}</p>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let tabs = document.querySelectorAll('.tab-link');
    let contents = document.querySelectorAll('.tab-content');

    // 初期状態で出品した商品タブを表示
    let activeTab = document.querySelector('.tab-link.active');
    let activeContent = document.getElementById(activeTab.getAttribute('data-tab'));

    // 初期状態で出品した商品だけ表示する
    contents.forEach(content => content.style.display = 'none');
    activeContent.style.display = 'block';

    tabs.forEach(tab => {
        tab.addEventListener('click', function (event) {
            event.preventDefault();

            let target = this.getAttribute('data-tab');

            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => {
                c.style.display = 'none';  // すべて非表示にする
            });

            this.classList.add('active');
            let targetContent = document.getElementById(target);
            targetContent.style.display = 'block';  // 対応するコンテンツを表示
        });
    });
});
</script>
@endsection
