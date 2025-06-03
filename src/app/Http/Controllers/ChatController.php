<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

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
	
			// チャットメッセージ（古い順）
			$messages = $order->chatMessages()
					->with('user')
					->orderBy('created_at')
					->get();
	
			// サイドバー用の取引一覧（productとして使用）
			// 自分が購入者 or 出品者 かで切り替え
			if ($order->user_id === $userId) { // 購入者 → 自分が購入した、進行中の他の取引
        $sidebarOrders = Order::where('user_id', $userId)
            ->where('status', '決済完了')
            ->where('finished', false)
            ->where('id', '!=', $order->id)
            ->with('product')
            ->get();
    	} else { // 出品者 → 自分の商品が購入された、進行中の取引
        $sidebarOrders = Order::whereHas('product', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', '決済完了')
            ->where('finished', false)
            ->where('id', '!=', $order->id)
            ->with('product')
            ->get();
    	}
	
			return view('chat.show', [
					'order' => $order,
					'partner' => $partner,
					'messages' => $messages,
					'orders' => $sidebarOrders,
			]);
	}
	

}
