<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;

class PurchaseController extends Controller
{
    public function index($itemId)
    {
        $product = Product::findOrFail($itemId);
        $paymentMethods = [
            'credit_card' => 'クレジットカード',
            'paypal' => 'PayPal',
            'bank_transfer' => '銀行振込'
        ];

        return view('purchase', compact('product', 'paymentMethods'));
    }

    public function purchase(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $order = new Order();
        $order->product_id = $product->id;
        $order->user_id = auth()->id();
        $order->payment_method_id = $request->payment_method;
        $order->shipping_address = auth()->user()->address; // 配送先
        // 必要な情報を追加して保存
        $order->save();

        return redirect()->route('order.complete', ['order' => $order->id]);
    }
}
