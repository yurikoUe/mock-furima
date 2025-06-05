@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')

<div class="chat">

    <!-- サイドバー -->
    <aside class="chat-sidebar">
        <h2 class="chat-sidebar__title">その他の取引</h2>
        @foreach ($orders as $sidebarOrder)
            <div class="chat-sidebar__item">
                <a href="{{ route('chat.show', $sidebarOrder->id) }}" class="chat-sidebar__link">
                    {{ $sidebarOrder->product->name }}
                </a>
            </div>
        @endforeach
    </aside>

    <!-- メインチャットエリア -->
    <main class="chat-main">

        <!-- 取引相手情報 -->
        <section class="chat-partner">
            <img src="{{ asset('storage/' . $partner->profile_image) }}" alt="相手のプロフィール画像" class="chat-partner__img">
            <p class="chat-partner__name">「{{ $partner->name }}」 さんとの取引画面</p>
            @if ($isBuyer)
                <button type="button" id="end-trade-btn" class="chat-partner__btn">取引を完了する</button>
            @endif
        </section>

        <!-- 区切り線 -->
        <div class="chat-divider"></div>

        <!-- 取引商品情報 -->
        <section class="chat-product">
            <img src="{{ asset('storage/' . $order->product->image) }}" alt="商品画像" class="chat-product__img">
            <div class="chat-product__text">
                <p class="chat-product__name">{{ $order->product->name }}</p>
                <p class="chat-product__price">¥{{ number_format($order->product->price) }}</p>
            </div>
        </section>

        <!-- 区切り線 -->
        <div class="chat-divider"></div>

        <!-- チャットメッセージ表示 -->
        <section class="chat-messages">
            @foreach ($messages as $message)
                @if ($message->sender_id === auth()->id())
                    <!-- 自分のメッセージ -->
                    <div class="chat-message__wrapper chat-message--self">
                        <div class="chat-message__header">
                            <img src="{{ asset('storage/' . $message->sender->profile_image) }}" alt="アイコン" class="chat-message__img">
                            <p class="chat-message__name">{{ $message->sender->name }}</p>
                        </div>

                        @if (request('edit') == $message->id)
                            <form action="{{ route('chat.update', ['order' => $order->id, 'message' => $message->id]) }}" method="POST" class="chat-message__form">
                                @csrf
                                @method('PUT')
                                <input type="text" name="content" value="{{ old('content', $message->content) }}" class="chat-message__input">
                                <button type="submit" class="chat-message__save-btn">保存</button>
                                <a href="{{ route('chat.show', $order->id) }}" class="chat-message__cancel-btn">キャンセル</a>
                                @error('content')
                                    <div class="chat-message__error">{{ $message }}</div>
                                @enderror
                            </form>
                        @else
                            <p class="chat-message__text">{{ $message->content }}</p>
                            @if ($message->image)
                                <img src="{{ asset('storage/' . $message->image) }}" alt="添付画像" class="chat-message__image">
                            @endif
                        @endif

                        @if (request('edit') != $message->id)
                            <div>
                                <a href="{{ route('chat.show', ['order' => $order->id, 'edit' => $message->id]) }}" class="chat-message__edit-btn">編集</a>
                                <form action="{{ route('chat.destroy', ['order' => $order->id, 'message' => $message->id]) }}" method="POST" class="chat-message__delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="chat-message__delete-btn">削除</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- 相手のメッセージ -->
                    <div class="chat-message__wrapper chat-message--partner">
                        <div class="chat-message__header">
                            <img src="{{ asset('storage/' . $message->sender->profile_image) }}" alt="アイコン" class="chat-message__img">
                            <p class="chat-message__name">{{ $message->sender->name }}</p>
                        </div>
                        <p class="chat-message__text">{{ $message->content }}</p>
                        @if ($message->image)
                            <img src="{{ asset('storage/' . $message->image) }}" alt="添付画像" class="chat-message__image">
                        @endif
                    </div>
                @endif
            @endforeach
        </section>

        <!-- メッセージ投稿フォーム -->
        <section class="chat-form">
            @if ($errors->any())
                <div class="chat-form__error">
                    <ul class="chat-form__error-list">
                        @foreach ($errors->all() as $error)
                            <li class="chat-form__error-item">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('chat.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="chat-form__form">
                @csrf
                <input
                    type="text"
                    name="content"
                    value="{{ old('content') }}"
                    placeholder="取引メッセージを記入してください"
                    class="chat-form__input"
                >

                <label class="chat-form__file-label">
                    画像を追加
                    <input type="file" name="image" class="chat-form__file-input">
                </label>

                <button type="submit" class="chat-form__submit-btn">
                    <img src="{{ asset('send-icon.jpg') }}" alt="送信" class="chat-form__submit-icon">
                </button>
            </form>
        </section>

        <!-- 取引完了モーダル（購入者用） -->
        <div id="trade-complete-modal" style="display:none;" class="rating-modal rating-modal--buyer">
            <div class="rating-modal__content">
                <h3 class="rating-modal__title">取引が完了しました</h3>
                <div class="chat-divider"></div>
                <p class="rating-modal__text">今回の取引相手はどうでしたか？</p>
                <form action="{{ route('orders.complete', $order->id) }}" method="POST" id="rating-form" class="rating-modal__form">
                    @csrf
                    <div class="rating-modal__stars">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}"/>
                            <label for="star{{ $i }}" title="{{ $i }} stars">★</label>
                        @endfor
                    </div>
                    <div class="chat-divider"></div>
                    <button type="submit" class="rating-modal__submit-btn">送信</button>
                </form>
            </div>
        </div>

        <!-- 取引完了モーダル（出品者用） -->
        <div id="seller-rating-modal" style="display:none;" class="rating-modal rating-modal--seller">
            <div class="rating-modal__content">
                <h3 class="rating-modal__title">購入者を評価してください</h3>
                <p class="rating-modal__text">{{ $partner->name }} さんとの取引を評価してください。</p>
                <form action="{{ route('orders.rate', $order->id) }}" method="POST" class="rating-modal__form">
                    @csrf
                    <div class="rating-modal__stars">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="seller-star{{ $i }}" name="rating" value="{{ $i }}" class="rating-modal__radio" />
                            <label for="seller-star{{ $i }}" class="rating-modal__label">★</label>
                        @endfor
                    </div>
                    <button type="submit" class="rating-modal__submit-btn">送信</button>
                </form>
            </div>
        </div>

    </main>
</div>

<!-- JavaScript：下書き保存・モーダル制御 -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="content"]');
    const key = 'chat_draft_{{ $order->id }}';
    input.addEventListener('input', function () {
        sessionStorage.setItem(key, input.value);
    });
    @if (!session('message_sent'))
        const saved = sessionStorage.getItem(key);
        if (saved && input.value === '') {
            input.value = saved;
        }
    @else
        sessionStorage.removeItem(key);
    @endif
});
</script>

@if ($showRateModal)
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('seller-rating-modal').style.display = 'block';
});
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('trade-complete-modal');
    const openBtn = document.getElementById('end-trade-btn');
    openBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

@endsection
