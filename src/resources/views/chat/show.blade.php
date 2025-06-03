@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')

<div class="chat-container flex">

    <!-- サイドバー -->
    <aside class="sidebar">
        <h2 class="sidebar-title">その他の取引</h2>
        @foreach ($orders as $sidebarOrder)
            <div class="sidebar-item">
                <a href="{{ route('chat.show', $sidebarOrder->id) }}" class="sidebar-link">
                    {{ $sidebarOrder->product->name }}
                </a>
            </div>
        @endforeach
    </aside>

    <!-- メインチャットエリア -->
    <main class="main-chat">

        <!-- 取引相手情報 -->
        <section class="partner-info flex items-center">
            <img src="{{ asset('storage/' . $partner->profile_image) }}" alt="相手のプロフィール画像" class="partner-image">
            <p class="partner-name">{{ $partner->name }} さんとの取引画面</p>
        </section>

        <!-- 区切り線 -->
        <div class="divider"></div>

        <!-- 取引商品情報 -->
        <section class="item-info flex items-center">
            <img src="{{ asset('storage/' . $order->product->image) }}" alt="商品画像" class="item-image">
            <div class="item-text">
                <p class="item-name">{{ $order->product->name }}</p>
                <p class="item-price">¥{{ number_format($order->product->price) }}</p>
            </div>
        </section>

        <!-- 区切り線 -->
        <div class="divider"></div>

        <!-- チャット画面 -->
        <section class="chat-messages">
            @foreach ($messages as $message)
                @if ($message->user_id === auth()->id())
                    <!-- 自分のメッセージ（右寄せ） -->
                    <div class="message message-self">
                        <div class="message-content">
                            <p class="message-username">{{ $message->user->name }}</p>
                            <p class="message-text">{{ $message->content }}</p>
                            <div class="message-actions">
                                <a href="{{ route('chat.edit', ['order' => $order->id, 'message' => $message->id]) }}" class="action-edit">編集</a>
                                <form action="{{ route('chat.destroy', ['order' => $order->id, 'message' => $message->id]) }}" method="POST" class="action-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-delete">削除</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- 相手のメッセージ（左寄せ） -->
                    <div class="message message-partner">
                        <img src="{{ asset('storage/' . $message->user->profile_image) }}" alt="アイコン" class="message-avatar">
                        <div class="message-content">
                            <p class="message-username">{{ $message->user->name }}</p>
                            <p class="message-text">{{ $message->content }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </section>

        <!-- メッセージ投稿フォーム -->
        <section class="message-form-section">
            <form action="{{ route('chat.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="message-form">
                @csrf
                <input type="text" name="content" placeholder="取引メッセージを記入してください" class="message-input">
                <label class="message-upload-label">
                    画像を追加
                    <input type="file" name="image" class="message-upload-input">
                </label>
                <button type="submit" class="message-submit-button">
                    <svg class="message-submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </form>
        </section>

    </main>
</div>

@endsection
