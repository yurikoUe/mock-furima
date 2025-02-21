<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Product $product)
    {
        $user = Auth::user();

        // すでにいいねしているか確認
        if (!$user->favorites()->where('product_id', $product->id)->exists()) {
            Favorite::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
        }

        return back();
    }

    public function destroy(Product $product)
    {
        $user = Auth::user();

        // いいねが存在する場合は削除
        $user->favorites()->where('product_id', $product->id)->delete();

        return back();
    }
}
