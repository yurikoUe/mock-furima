@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item-detail.css') }}">
@endsection

@section('content')
<div class="product-detail">
    <div class="product-detail__image">
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    </div>
    
    <div class="product-detail__info">
        <h1 class="product-detail__name">{{ $product->name }}</h1>
        
        <p class="product-detail__price">¥{{ number_format($product->price) }}(税込)</p>
        <div class="product-detail__icon">

            <!-- いいね機能 -->
            <div class="product-detail__favorites">
                {{-- ★ 未ログイン時のスターアイコン表示 --}}
                @guest
                    <span class="material-icons">star_outline</span>
                @endguest

                {{-- ★ ログインユーザーのいいねボタン --}}
                @auth
                    @if($product->isFavoritedBy(Auth::user()))
                        <form action="{{ route('favorite.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:none; border:none; cursor:pointer; display:inline-flex; align-items:center;">
                                <span class="material-icons" style="color: gold;">star</span>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('favorite.store', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:none; border:none; cursor:pointer; display:inline-flex; align-items:center;">
                                <span class="material-icons">star_outline</span>
                            </button>
                        </form>
                    @endif
                @endauth
                {{-- ★ お気に入り数（ログインしていなくても見える） --}}
                <p>{{ $product->favorites()->count() }}</p>
            </div>

            <div class="product-detail__comments">
                    <span class="material-icons">chat_bubble_outline</span>
                    <p>{{ $product->comments()->count() }}</p>
            </div>
        </div>
        <!-- 購入手続き -->
        @if (!$sold)
            <a href="{{ route('purchase', ['item_id' => $product->id]) }}" class="product-detail__purchase-button">
                購入手続きへ
            </a>
        @endif
        <!-- 商品が注文されている場合 -->
        @php
            $sold = \App\Models\Order::where('product_id', $product->id)->exists();
        @endphp
        @if ($sold)
            <span class="product-detail__sold">Sold</span>
        @endif

        <h2>商品説明</h2>
        <p class="product-detail__description">{{ $product->description }}</p>
        <h2>商品の情報</h2>
        <table class="product-detail__table">
            <tr>
                <th>カテゴリー</th>
                <td class="product-detail__categories">
                    @foreach ($product->categories as $category)
                        <span class="product-detail__category">{{ $category->name }}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>商品の状態</th>
                <td class="product-detail__condition">{{ $product->condition->name }}</td>
            </tr>
        </table>

        <h2 class="product-detail__comment-title">コメント ({{ $product->comments()->count() }})</h2>
        <!-- commentsテーブルに関連コメントがある場合はこちらに表示。コメントした人の画像と名前、コメントの３つを表示 -->
        @foreach ($product->comments as $comment)
            <div class="comment">
                <div class="comment__user">
                    <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="{{ $comment->user->name }}" class="comment__user-image">
                    <span class="comment__user-name">{{ $comment->user->name }}</span>
                </div>
                <p class="comment__text">{{ $comment->content }}</p>
            </div>
        @endforeach

        <h3 class="comment__form-title">商品へのコメント</h3>
        <form action="{{ route('product.comment', $product->id) }}" method="POST" class="comment__form">
            @csrf
            <textarea name="comment" rows="5"></textarea>
            @error('comment')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <button type="submit" class="product-detail__comment-submit">コメントを送信する</button>
        </form>

        
    </div>
</div>
@endsection
