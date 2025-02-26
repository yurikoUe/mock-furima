<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;

class PurchaseController extends Controller
{
    public function purchase($itemId)
    {
        $product = Product::findOrFail($itemId);
        $paymentMethods = Order::PAYMENT_METHODS;
        $user = auth()->user();

        // profile_completed が false なら住所登録ページにリダイレクト
        if (!$user->profile_completed) {
            return redirect()->route('mypage.profile')->with('error', '購入前に住所を登録してください。');
        }
        
        // この商品がすでに売り切れかチェック
        $isSold = Order::where('product_id', $product->id)
                    ->where('status', '完了')
                    ->exists();

        if ($isSold) {
            return redirect()->route(' purchase', ['id' => $product->id])->with('error', 'この商品はすでに売り切れています。');
        }
        
        // 配送先住所の変更がある場合、Orderテーブルから最初の住所を取得
        $orderAddress = session('orderAddress', null);  // セッションから取得

        // もし配送先住所の変更がなければ、ユーザーのデフォルト住所を表示
        if (!$orderAddress) {
            $orderAddress = Order::where('user_id', auth()->id())->first();

            // もし注文がない場合、ユーザーのデフォルト住所を表示
            if (!$orderAddress) {
                $orderAddress = (object)[
                    'order_postal_code' => auth()->user()->postal_code,
                    'order_address' => auth()->user()->address,
                    'order_building' => auth()->user()->building,
                ];
            }
        }

        return view('purchase', compact('product', 'paymentMethods', 'orderAddress', 'isSold'));
    }


    public function store(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        // すでに「未完了」の注文があるかチェック
        $order = Order::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->where('status', '未完了')
                    ->first();

        // 未完了の注文があれば上書き、なければ新規作成
        if (!$order) {
            $order = new Order();
            $order->user_id = auth()->id();
            $order->product_id = $product->id;
        }

        // フォームから送られたデータをセット
        $order->payment_method = $request->payment_method;
        $order->order_address = $request->order_address;
        $order->order_postal_code = $request->order_postal_code;
        $order->order_building = $request->order_building;

        // **購入完了時にステータスを「完了」に更新**
        $order->status = '完了';

        $order->save();

        $product->save();

        // 購入完了画面へリダイレクト
        return redirect()->route('index');
    }

    public function showAddressChangeForm($item_id)
    {
        $product = Product::findOrFail($item_id);
        return view('address-change', compact('product', 'item_id'));
    }

    public function saveAddress(Request $request, $itemId)
    {
        // バリデーション
        $validatedData = $request->validate([
            'order_postal_code' => 'required|string|max:255',
            'order_address' => 'required|string|max:255',
            'order_building' => 'nullable|string|max:255',
        ]);

        // 現在の認証ユーザーを取得
        $user = auth()->user();

        // 新規オーダーを作成し、住所情報を保存
        $order = new Order();
        $order->user_id = auth()->id();
        $order->product_id = $itemId; // 商品IDも指定
        $order->order_address = $validatedData['order_address'];
        $order->order_postal_code = $validatedData['order_postal_code'];
        $order->order_building = $validatedData['order_building'];
        $order->status = '未完了';  // 注文ステータス（例: 未完了）

        $order->save();

        return redirect()->route('purchase', ['item_id' => $itemId])
                        ->with('success', '住所が変更されました。')
                        ->with('orderAddress', $order); 
    }
}
