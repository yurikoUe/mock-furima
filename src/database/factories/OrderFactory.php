<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),  // ランダムなユーザーを関連付け
            'order_address_id' => OrderAddress::factory(),  // ランダムな注文住所を関連付け
            'product_id' => Product::factory(),  // ランダムな製品を関連付け
            'payment_method' => $this->faker->randomElement(['card', 'convenience_store']),  // 支払い方法
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']),  // 注文のステータス
        ];
    }
}
