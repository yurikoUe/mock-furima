<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        // profile_completed が false なら住所登録ページにリダイレクト
        if (!$user->profile_completed) {
            return redirect()->route('mypage.profile')->with('error', '購入前に住所を登録してください。');
        }

        $tab = request('tab', 'sell');  // デフォルトで'sell'タブを選択

        // 出品した商品
        $sellingProducts = Product::where('user_id', $user->id)->get();

        // 購入した商品
        $purchasedProducts = Order::where('user_id', $user->id)
                                ->with('product')  // 購入した商品情報も一緒に取得
                                ->get();

        $chatOrders = Order::activeForUser($user->id) // スコープを使って絞り込み
                    ->with('product')                         // 商品情報を取得
                    ->withCount('unreadMessages')             // 未読メッセージ数を取得
                    ->get();

        return view('mypage', compact('user', 'sellingProducts', 'purchasedProducts', 'chatOrders', 'tab'));
    }

    public function edit()
    {
        return view('profile-edit');
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // 画像がアップロードされた場合の処理
        if ($request->hasFile('profile_image')) {
            // 既存の画像があれば削除
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }

            // 新しい画像を保存
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // 他の情報を更新
        $user->update([
            'name' => $request->name,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        // プロフィール完了フラグを設定
        $user->profile_completed = true;
        $user->save();

        return redirect()->route('mypage.profile')->with('success', 'プロフィールを更新しました！');

    }
}

