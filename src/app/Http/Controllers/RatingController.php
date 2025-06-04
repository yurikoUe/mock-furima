<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RatingController extends Controller
{
    // 購入者が「取引完了」押して評価送信、取引終了フラグ立て、通知メール送信
    public function completeAndRate(Request $request, Order $order)
    {
        $user = Auth::user();

        // ここでユーザーが購入者かどうかのチェック（例）
        if ($order->user_id !== $user->id) {
            abort(403, '権限がありません');
        }

        // バリデーション
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // 取引終了フラグ立てる
        $order->finished = true;
        $order->save();

        // 評価保存（購入者→出品者）
        Rating::create([
            'order_id' => $order->id,
            'rater_id' => $user->id,
            'ratee_id' => $order->product->user_id,
            'rating' => $data['rating'],
        ]);

        // 出品者にメール送信
        $seller = $order->product->user;
        Mail::to($seller->email)->send(new \App\Mail\OrderCompletedNotification(
            $user->name, // $buyerName（購入者）
            $order->product->name, //$productName
            $order->id, //$orderId
            $seller->name, //sellerName(出品者)
        ));

        return redirect()->route('index')->with('success', '取引が完了し、評価を送信しました。');

    }

    // 出品者が購入者を評価するための処理（finished=trueの注文に対して）
    public function rate(Request $request, Order $order)
    {
        $user = Auth::user();

        // 出品者であることを確認
        if ($order->product->user_id !== $user->id) {
            abort(403, '権限がありません');
        }

        // バリデーション
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // 評価保存（出品者→購入者）
        Rating::create([
            'order_id' => $order->id,
            'rater_id' => $user->id,
            'ratee_id' => $order->user_id,
            'rating' => $data['rating'],
        ]);

        return redirect()->route('index')->with('success', '評価を送信しました。');

    }
}
