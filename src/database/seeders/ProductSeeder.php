<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userA = User::where('email', 'usera@example.com')->first();
        $userB = User::where('email', 'userb@example.com')->first();

        // 各テーブルのIDを取得
        $categories = Category::pluck('id', 'name')->toArray();
        $brands = Brand::pluck('id', 'name')->toArray();

        // 商品データ
        $products = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img' => 'images/Armani_Mens_Clock.jpg',
                'condition' => '良好',
                'category_names' => ['ファッション']
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'img' => 'images/HDD_Hard_Disk.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_names' => ['家電']
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'img' => 'images/iLoveIMG_d.jpg',
                'condition' => 'やや傷や汚れあり',
                'category_names' => ['キッチン']
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'img' => 'images/Leather_Shoes_Product_Photo.jpg',
                'condition' => '状態が悪い',
                'category_names' => ['ファッション']
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'img' => 'images/Living_Room_Laptop.jpg',
                'condition' => '良好',
                'category_names' => ['家電']
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'img' => 'images/Music_Mic_4632231.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_names' => ['家電']
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'img' => 'images/Purse_fashion_pocket.jpg',
                'condition' => 'やや傷や汚れあり',
                'category_names' => ['レディース']
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'img' => 'images/Tumbler_souvenir.jpg',
                'condition' => '状態が悪い',
                'category_names' => ['家電', 'キッチン']
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'img' => 'images/Waitress_with_Coffee_Grinder.jpg',
                'condition' => '良好',
                'category_names' => ['家電', 'キッチン']
            ],
            [
                'name' => 'メイクセット',
                'price' => 25000,
                'description' => '便利なメイクアップセット',
                'img' => 'images/Going_out_makeup_set.jpg',
                'condition' => '目立った傷や汚れなし',
                'category_names' => ['コスメ']
            ],
        ];

        foreach ($products as $index => $productData) {
                $product = Product::create([
                'name' => $productData['name'],
                'user_id' => $index < 5 ? $userA->id : $userB->id,
                'price' => $productData['price'],
                'description' => $productData['description'],
                'image' => $productData['img'],
                'condition' => $productData['condition'], 
                'brand_id' => collect($brands)->random(), // ランダムにブランドを設定
            ]);

            // 商品とカテゴリーを関連付ける
            $product->categories()->attach(
                array_map(fn($name) => $categories[$name], $productData['category_names'])
            );
        }
    }
}
