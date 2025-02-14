<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Product;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // ログインしている場合
        if (Auth::check()) {
            $user = Auth::user();

            // 初めてのログイン（プロフィールが未完了の場合）
            if ($user->profile_completed == false) {
                return Redirect::route('mypage.profile');
            }
        }

        // タブが「mylist」の場合
        if ($request->get('tab') == 'mylist') {
            // ログインしているユーザーのマイリスト商品を取得
            $products = Auth::check() ? Auth::user()->favorites()->get() : [];
        } else {
            // タブが「おすすめ」の場合（おすすめ商品を表示）
            // 仮に新着順の商品を取得
            $products = Product::orderBy('created_at', 'desc')->get();
        }

        // 商品データをビューに渡す
        return view('index', compact('products'));
    }
}
