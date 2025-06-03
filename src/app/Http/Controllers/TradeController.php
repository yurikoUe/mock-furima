<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function rate(Request $request, Order $order)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $rater = auth()->user();

        // 出品者と購入者のIDを取得
        $buyerId = $order->user_id;
        $sellerId = $order->product->user_id;


        // 保存
        \App\Models\Rating::create([
            'order_id' => $order->id,
            'rater_id' => $rater->id,
            'ratee_id' => $ratee->id,
            'rating' => $request->rating,
        ]);

        return redirect()->route('chat.show', $order->id);
    }

}
