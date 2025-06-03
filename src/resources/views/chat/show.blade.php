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
			@if ($isBuyer)
				<button type="button" class="btn btn-primary" id="end-trade-btn">取引終了</button>
			@endif
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

		<section class="chat-messages">
    @foreach ($messages as $message)
        @if ($message->sender_id === auth()->id())
            <!-- 自分のメッセージ（右寄せ） -->
            <div class="message message-self">
                <div class="message-content">
                    <p class="message-username">{{ $message->sender->name }}</p>

                    {{-- 編集モードならフォームを表示 --}}
                    @if (request('edit') == $message->id)
                        <form action="{{ route('chat.update', ['order' => $order->id, 'message' => $message->id]) }}" method="POST" class="edit-form">
                            @csrf
                            @method('PUT')
							
                            <input type="text" name="content" value="{{ old('content', $message->content) }}" class="edit-input">
                            <button type="submit" class="btn-save">保存</button>
                            <a href="{{ route('chat.show', $order->id) }}" class="btn-cancel">キャンセル</a>
							@error('content')
								<div class="text-red-500">{{ $message }}</div>
							@enderror
                        </form>
                    @else
                        <p class="message-text">{{ $message->content }}</p>
                    @endif

                    @if ($message->image)
                        <img src="{{ asset('storage/' . $message->image) }}" alt="商品画像" class="item-image">
                    @endif
                </div>
                @if (request('edit') != $message->id)
                    <div class="message-actions">
                        <a href="{{ route('chat.show', ['order' => $order->id, 'edit' => $message->id]) }}" class="action-edit">編集</a>
                        <form action="{{ route('chat.destroy', ['order' => $order->id, 'message' => $message->id]) }}" method="POST" class="action-delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-delete">削除</button>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <!-- 相手のメッセージ（左寄せ） -->
            <div class="message message-partner">
                <img src="{{ asset('storage/' . $message->sender->profile_image) }}" alt="アイコン" class="message-avatar">
                <div class="message-content">
                    <p class="message-username">{{ $message->sender->name }}</p>
                    <p class="message-text">{{ $message->content }}</p>
                </div>
            </div>
        @endif
    @endforeach
</section>


		<!-- メッセージ投稿フォーム -->
		<section class="message-form-section">
			@if ($errors->any())
				<div class="mb-4 text-red-600">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif
		
			<form action="{{ route('chat.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="message-form">
				@csrf
				<input type="text" name="content" value="{{ old('content') }}" placeholder="取引メッセージを記入してください" class="message-input">
				<label class="message-upload-label">
					画像を追加
					<input type="file" name="image" class="message-upload-input">
				</label>
				<button type="submit" class="message-submit-button">
					<img src="{{ asset('send-icon.svg') }}" alt="送信" class="message-submit-icon">
				</button>
			</form>
		</section>

		<!-- 取引終了モーダル -->
<div id="trade-complete-modal" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>取引が完了しました</h3>
		<p>今回の取引相手はどうでしたか？</p>
        <form action="{{ route('trade.rate', $order->id) }}" method="POST" id="rating-form">
            @csrf
            <div class="star-rating">
                @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" />
                    <label for="star{{ $i }}" title="{{ $i }} stars">★</label>
                @endfor
            </div>
            <button type="submit" class="btn btn-primary">送信</button>
        </form>
    </div>
</div>

	</main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	const input = document.querySelector('input[name="content"]');
	const key = 'chat_draft_{{ $order->id }}';

	// 入力を保存
	input.addEventListener('input', function () {
		sessionStorage.setItem(key, input.value);
	});

	// ★ Laravelセッションで「送信された」場合は復元しない
	@if (!session('message_sent'))
		const saved = sessionStorage.getItem(key);
		if (saved && input.value === '') {
			input.value = saved;
		}
	@else
		// セッションに送信済みフラグがある → draft 削除
		sessionStorage.removeItem(key);
	@endif
});


document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('trade-complete-modal');
    const openBtn = document.getElementById('end-trade-btn');
    const cancelBtn = document.getElementById('modal-cancel-btn');

    openBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    cancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // モーダル外クリックで閉じる（任意）
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

</script>



@endsection
