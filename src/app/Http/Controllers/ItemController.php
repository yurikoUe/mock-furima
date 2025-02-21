<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Http\Requests\CommentRequest;

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

        $keyword = $request->get('keyword');
        $tab = $request->get('tab');

        // クエリビルダーを使って検索を適用
        if ($request->get('tab') == 'mylist') {
            // 「マイリスト」の場合
            if (Auth::check()) {
                $query = Product::whereIn('id', Auth::user()->favorites()->pluck('product_id'));
            } else {
                $query = Product::whereRaw('1 = 0'); // 未ログインなら空データを返す
            }
        } else {
            // 「おすすめ」（新着順の商品）
            $query = Product::orderBy('created_at', 'desc');
        }

        // 検索ワードがある場合、部分一致検索を適用
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // 商品を取得
        $products = $query->get();

        // 商品データと検索ワードをビューに渡す
        return view('index', compact('products', 'keyword'));
    }

    public function show($item_id)
    {
        $product = Product::findOrFail($item_id);
        $sold = Order::where('product_id', $product->id)->exists();
        return view('item-detail', compact('product', 'sold'));
    }

    // 商品コメント保存用のコントローラーアクション
    public function storeComment(CommentRequest $request, $productId)
    {
        // コメントを取得
        $product = Product::findOrFail($productId);
        // コメントを保存
        $product->comments()->create([
            'user_id' => auth()->id(), // ログインユーザーのID
            'product_id' => $productId, // 対象商品のID
            'content' => $request->comment, // 入力されたコメント
        ]);

        // 商品詳細ページにリダイレクト
        return redirect()->route('product.show', $productId);
    }
}