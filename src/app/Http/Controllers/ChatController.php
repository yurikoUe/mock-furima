<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Rating;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreChatMessageRequest;

class ChatController extends Controller
{
	public function show(Order $order)
	{
    	$userId = auth()->id();

		// アクセス制限：購入者か出品者のみ
		if (! $this->isOrderParticipant($order, $userId)) {
		abort(403, 'この取引にアクセスする権限がありません。');
		}

		// チャット相手
		$partner = $order->user_id === $userId
				? $order->product->user  // 相手は出品者
				: $order->user;          // 相手は購入者

		// 未読メッセージを既読に更新
		$order->chatMessages()
			->where('sender_id', '!=', $userId)
			->where('is_read', false)
			->update(['is_read' => true]);

		// チャットメッセージ一覧（古い順）
		$messages = $order->chatMessages()
				->with('sender')
				->orderBy('created_at')
				->get();

		// サイドバー用：他の進行中の取引一覧
		$sidebarOrders = $this->getSidebarOrders($order, $userId);
	
		// 購入者かどうか
		$isBuyer = $order->user_id === $userId;

		// 出品者かどうか
		$isSeller = $order->product->user_id === $userId;

    	$hasAlreadyRated = Rating::where('order_id', $order->id)
			->where('rater_id', $userId)
			->exists();

    	$showRateModal = $isSeller && $order->finished && !$hasAlreadyRated;

		return view('chat.show', [
			'order' => $order,
			'partner' => $partner,
			'messages' => $messages,
			'orders' => $sidebarOrders,
			'isBuyer' => $isBuyer,
			'showRateModal' => $showRateModal,
		]);
	}
	
	public function store(StoreChatMessageRequest $request, Order $order)
	{
		$imagePath = $request->hasFile('image')
			? $request->file('image')->store('chat_images', 'public')
			: null;

    	$validated = $request->validated();

		$order->chatMessages()->create([
			'sender_id' => Auth::id(),
			'content' => $validated['content'],
			'image' => $imagePath,
			'is_read' => false,
		]);

		return redirect()->route('chat.show', $order->id)->with('message_sent', true);

	}

	public function edit(Order $order, ChatMessage $message)
	{
		$this->authorizeMessageAccess($order, $message);

		return view('chat.edit', [
				'order' => $order,
				'message' => $message,
		]);
	}

	public function update(StoreChatMessageRequest $request, Order $order, ChatMessage $message)
	{
		$this->authorizeMessageAccess($order, $message);

    	$validated = $request->validated();

		$message->update([
				'content' => $validated['content'],
		]);

		return redirect()->route('chat.show', $order->id);
	}

	public function destroy(Order $order, ChatMessage $message)
	{
		$this->authorizeMessageAccess($order, $message);

		// 画像削除（あれば）
		if ($message->image) {
				Storage::disk('public')->delete($message->image);
		}

		$message->delete();

		return redirect()->route('chat.show', $order->id);
	}

	/**
     * 指定されたメッセージの所有権と紐づけを確認
     */
	private function authorizeMessageAccess(Order $order, ChatMessage $message)
	{
		/// チャットメッセージが指定された注文に属していなければ４０４
		if ($message->order_id !== $order->id) {
			abort(404);
		}

		// ログインユーザーがこのメッセージの投稿者でない場合は４０３
		if ($message->sender_id !== Auth::id()) {
				abort(403);
		}
	}

	/**
     * ログインユーザーが取引の関係者かチェック
     */
	private function isOrderParticipant(Order $order, int $userId): bool
	{
		return $order->user_id === $userId ||$order->product->user_id === $userId;
	}

	private function getSidebarOrders(Order $currentOrder, int $userId)
	{
		return Order::activeForUser($userId)
			->where(function ($query) use ($userId) {
				$query->where('finished', false)
					->orWhere(function ($q) use ($userId) {
						$q->where('finished', true)
							->whereHas('product', fn ($q2) => $q2->where('user_id', $userId)) // 出品者
							->whereDoesntHave('rating', fn($q3) => $q3->where('rater_id', $userId));
					});
			})
			->where('id', '!=', $currentOrder->id)
			->with('product')
			->get();
	}

}
