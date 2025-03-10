<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;

class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'user_id' => User::factory(), // ランダムなユーザーを作成
            'price' => $this->faker->numberBetween(300, 50000),
            'description' => $this->faker->sentence(),
            'image' => 'images/' . $this->faker->word() . '.jpg', // 仮の画像パス
            'condition' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'brand_id' => Brand::factory(), // ランダムなブランド
        ];
    }
    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // ランダムなカテゴリーを 1～3 つ関連付ける
            $categories = Category::inRandomOrder()->limit(rand(1, 3))->pluck('id');
            $product->categories()->attach($categories);
        });
    }
}
