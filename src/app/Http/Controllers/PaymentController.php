<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderAddress;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PurchaseRequest;


class PaymentController extends Controller
{
    public function checkout(PurchaseRequest $request)
    {
        Stripe::setApiKey(config('stripe.secret'));

        $user = auth()->user();
        $product = Product::findOrFail($request->product_id);
        $orderAddress = OrderAddress::findOrFail($request->order_address_id);
        $paymentMethod = $request->payment_method;

        // 支払い方法を判定
        $stripePaymentMethod = ($paymentMethod === 'card') ? ['card'] : ['konbini'];

        // Stripe Customer の作成（ログインユーザーの情報を使用）
        $customer = \Stripe\Customer::create([
            'email' => $user->email,
            'name' => $user->name,
        ]);

        // 商品情報をStripeで作成
        $stripeProduct = \Stripe\Product::create([
            'name' => $product->name,
            'description' => $product->description, // 商品説明を追加
        ]);

        // 価格を指定してStripeの価格を作成
        $stripePrice = \Stripe\Price::create([
            'unit_amount' => $product->price,
            'currency' => 'jpy',
            'product' => $stripeProduct->id,  // 先に作成した商品IDを指定
        ]);

         // 注文を保存（決済前に注文を作成）
        $order = new Order();
        $order->user_id = auth()->id();
        $order->product_id = $product->id;
        $order->order_address_id = $orderAddress->id;
        $order->payment_method = $paymentMethod;
        $order->status = '決済待機中';  // 初期ステータスを設定
        $order->save();

        try {

        // 決済セッションを作成
        $session = \Stripe\Checkout\Session::create([
            'customer' => $customer->id,
            // 'payment_method_types' => ['card', 'konbini'],
            'payment_method_types' => $stripePaymentMethod,
            'line_items' => [[
                'price' => $stripePrice->id, // 作成した価格IDを指定
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'order_id' => $order->id,
            ]
        ]);

        // セッションURLへリダイレクト
        return redirect($session->url);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());

            $order->status = '決済失敗';  // 初期ステータスを設定
            $order->save();

            return back()->with('error', '決済処理に失敗しました。再度お試しください');
        }
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('stripe.secret'));

        $sessionId = $request->input('session_id');

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $order = Order::where('id', $session->metadata->order_id)->first();
            $user = auth()->user();

            // 二重決済を防止するためのチェック
            if ($order->status === '決済完了') {
                return redirect()->route('index')->with('info', 'この注文は既に決済されています');
            }

            if ($session->payment_status === 'paid') {

                // すでに注文に紐づく支払い方法を取得
                $paymentMethod = $order->payment_method;

                // クレジットカード決済なら請求書は作成しない
                if ($paymentMethod === 'convenience_store') {
                    // Stripeの請求書を作成してメール送信（コンビニ決済の場合のみ）
                    $invoice = \Stripe\Invoice::create([
                        'customer' => $session->customer, // Checkoutの顧客IDを指定
                        'collection_method' => 'send_invoice', // メールで請求書を送信
                        'days_until_due' => 3, // 3日以内に支払い
                    ]);
                }

                $order->status = '決済完了';
                $order->save();
            }

            return redirect()->route('index')->with('success', '購入が完了しました！');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());

            // 決済確認に失敗した場合、ステータスを「要確認」に変更
            $order->payment_method = $paymentMethod;
            $order->status = '決済確認失敗';
            $order->save();

            return back()->with('error', '決済の確認に失敗しました。サポートまでお問い合わせください');
        }
    }

    public function cancel()
    {
        return redirect()->route('index')->with('cancel', '購入をキャンセルしました');
    }
}

