<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderAddress;
use App\Http\Requests\OrderAddressRequest;


class PurchaseController extends Controller
{
    public function create($itemId)
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
                    ->whereIn('status', ['決済完了', '決済待機中'])
                    ->exists();

        if ($isSold) {
            return redirect()->route(' purchase', ['id' => $product->id])->with('error', '購入されようとした商品はすでに売り切れています。');
        }
        
        // order_addressesテーブルに保存された直近の配送先住所を取得
        $orderAddress = OrderAddress::where('user_id', $user->id)->latest()->first();

        // もし注文履歴がない場合、ユーザーの登録住所をorder_addressesテーブルに保存
        if (!$orderAddress) {
            $orderAddress = new OrderAddress([
                'user_id' => $user->id, 
                'order_postal_code' => $user->postal_code,
                'order_address' => $user->address,
                'order_building' => $user->building,
            ]);
            $orderAddress->save();
        }

        return view('purchase', compact('product', 'paymentMethods', 'orderAddress', 'isSold'));
        
    }

    public function editAddress($item_id)
    {
        $product = Product::findOrFail($item_id);
        return view('address-change', compact('product', 'item_id'));
    }

    public function updateAddress(OrderAddressRequest $request, $itemId)
    {
        // 現在の認証ユーザーを取得
        $user = auth()->user();

        // 注文に関連する住所情報を order_addresses テーブルに保存
        $orderAddress = new OrderAddress();
        $orderAddress->user_id = auth()->id();
        $orderAddress->order_address = $validatedData['order_address'];
        $orderAddress->order_postal_code = $validatedData['order_postal_code'];
        $orderAddress->order_building = $validatedData['order_building'];

        $orderAddress->save(); // 住所情報を保存

        return redirect()->route('purchase.create', ['item_id' => $itemId])
                        ->with('success', '住所が変更されました。')
                        ->with('orderAddress', $orderAddress); 
    }
}
