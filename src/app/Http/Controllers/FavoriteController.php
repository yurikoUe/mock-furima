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

        // ユーザーがすでにお気に入りにしているか確認
        if (Auth::user()->favorites()->where('product_id', $product->id)->exists()) {
            return redirect()->back();
        }

        Auth::user()->favorites()->attach($product->id);

        return back();
    }

    public function destroy(Product $product)
    {

        // いいねが存在する場合は削除
        Auth::user()->favorites()->detach($product->id);

        return back();
    }
}
