<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;

class ExhibitionController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        // ealry return
        // profile_completed が false なら住所登録ページにリダイレクト
        if (!$user->profile_completed) {
            return redirect()->route('mypage.profile')->with('error', '購入前に住所を登録してください。');
        }
        
        $products = Product::all();
        $categories = Category::all();
        $brands = Brand::all();
        $conditions = ['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'];


        return view('sell', compact('conditions', 'products', 'categories', 'brands'));
    }

    public function store(ExhibitionRequest $request)
    {
        // 画像の保存
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $product = Product::create([
            $product->name = $request->name;
            $product->user_id = auth()->id();
            $product->description = $request->description;
            $product->condition = $request->condition;
            $product->price = $request->price;
            $product->image = $imagePath;
            $product->brand_id = $request->brand ? $request->brand : null;
        ]);

        // 商品とカテゴリーの関連付け
        $product->categories()->attach(array_map('intval', $request->category_id));

        // 成功メッセージと共にリダイレクト
        return redirect()->route('index')->with('success', '商品が出品されました！');
        
    }
}
