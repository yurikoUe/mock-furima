<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Brand;
use Illuminate\Http\Request;

class ExhibitionController extends Controller
{
    public function create()
    {
        $categories = Category::all();  // カテゴリーデータを取得
        $conditions = Condition::all(); // 商品の状態データを取得
        $brands = Brand::all();         // ブランドデータを取得

        return view('sell', compact('categories', 'conditions', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'condition' => 'required|exists:conditions,id',
            'price' => 'required|numeric',
            'brand' => 'required|exists:brands,id',
        ]);

        // 画像の保存
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $product = new Product();
        $product->name = $request->name;
        $product->user_id = auth()->id();
        $product->description = $request->description;
        $product->condition_id = $request->condition;
        $product->price = $request->price;
        $product->image = $imagePath;
        $product->brand_id = $request->brand;

        $product->save();  // 明示的に保存

        // 商品とカテゴリーの関連付け
        $product->categories()->attach(array_map('intval', $request->category_id));

        // 成功メッセージと共にリダイレクト
        return redirect()->route('index')->with('success', '商品が出品されました！');

        
    }
}
