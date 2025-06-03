<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreChatMessageRequest;

class ChatController extends Controller
{
	public function show(Order $order)
	{
    $userId = auth()->id();
	
    // アクセス制限（購入者か出品者のみ許可）
    if ($order->user_id !== $userId && $order->product->user_id !== $userId) {
      abort(403, 'この取引にアクセスする権限がありません。');
    }
	
			// チャット相手
			$partner = $order->user_id === $userId
					? $order->product->user  // 自分が購入者 → 相手は出品者
					: $order->user;          // 自分が出品者 → 相手は購入者
      
      // 自分以外が送った未読メッセージを既読に更新
      $order->chatMessages()
      ->where('sender_id', '!=', $userId)
      ->where('is_read', false)
      ->update(['is_read' => true]);
	
			// チャットメッセージ（古い順）
			$messages = $order->chatMessages()
					->with('sender')
					->orderBy('created_at')
					->get();
	
			// サイドバー用の取引一覧（購入者か出品者か関係なく、進行中の取引すべて）
      $sidebarOrders = Order::where(function ($query) use ($userId) {
        $query->where('user_id', $userId)  // 自分が購入者
            ->orWhereHas('product', function ($q) use ($userId) {
                $q->where('user_id', $userId); // 自分が出品者
            });
    })
    ->where('status', '決済完了')
    ->where('finished', false)
    ->where('id', '!=', $order->id)
    ->with('product')
    ->get();
	
    // 自分が購入者かどうかのフラグをビューに渡す
    $isBuyer = $order->user_id === $userId;

			return view('chat.show', [
					'order' => $order,
					'partner' => $partner,
					'messages' => $messages,
					'orders' => $sidebarOrders,
          'isBuyer' => $isBuyer,
			]);
	}
	
	public function store(StoreChatMessageRequest $request, Order $order)
	{
		// 画像保存
		$imagePath = null;
		if($request->hasFile('image')){
			$imagePath = $request->file('image')->store('chat_images', 'public');
		}

    $validated = $request->validated();

		// メッセージ保存
		$message = $order->chatMessages()->create([
			'sender_id' => Auth::id(),
			'content' => $validated['content'],
			'image' => $imagePath,
			'is_read' => false,
		]);

		return redirect()->route('chat.show', $order->id)->with('message_sent', true);

	}

	public function edit(Order $order, ChatMessage $message)
	{
		// 所属チェック
		if ($message->order_id !== $order->id) {
				abort(404);
		}

		// 投稿者チェック
		if ($message->sender_id !== auth()->id()) {
				abort(403);
		}

		// 編集画面用にメッセージと注文情報をビューに渡す
		return view('chat.edit', [
				'order' => $order,
				'message' => $message,
		]);
	}

	public function update(StoreChatMessageRequest $request, Order $order, ChatMessage $message)
	{
		// 所属チェック
		if ($message->order_id !== $order->id) {
				abort(404);
		}

		// 投稿者チェック
		if ($message->sender_id !== Auth::id()) {
				abort(403);
		}

    $validated = $request->validated();

		// 更新
		$message->update([
				'content' => $validated['content'],
		]);

		return redirect()->route('chat.show', $order->id)->with('success', 'メッセージを更新しました');
	}

	public function destroy(Order $order, ChatMessage $message)
	{
		// 所属チェック
    if ($message->order_id !== $order->id) {
			abort(404);
		}

		// 投稿者チェック
		if ($message->sender_id !== Auth::id()) {
				abort(403);
		}

		// 画像削除（あれば）
		if ($message->image) {
				Storage::disk('public')->delete($message->image);
		}

		// レコード削除
		$message->delete();

		return redirect()->route('chat.show', $order->id)->with('success', 'メッセージを削除しました');
	}

}
