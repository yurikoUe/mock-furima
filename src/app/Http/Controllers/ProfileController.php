<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $sellingProducts = Product::where('user_id', $user->id)->get();
        $purchasedProducts = Order::where('user_id', $user->id)
                            ->with('product')  // 購入した商品情報も一緒に取得
                            ->get();
        return view('mypage', compact('user', 'sellingProducts', 'purchasedProducts'));
    }

    public function edit()
    {
        return view('profile-edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building = $request->building;
        $user->profile_completed = true; // プロフィールが完了したことをマーク
        $user->save();

        return redirect()->route('mypage.profile')->with('success', 'プロフィールを更新しました！');

    }
}

