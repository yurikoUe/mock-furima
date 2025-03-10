<?php

namespace Database\Factories;

use App\Models\OrderAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderAddressFactory extends Factory
{
    protected $model = OrderAddress::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),  // ランダムにユーザーを関連付け
            'order_address' => $this->faker->address,  // ランダムな住所を生成
            'order_postal_code' => $this->faker->postcode,  // ランダムな郵便番号を生成
            'order_building' => $this->faker->word,  // ランダムな建物名を生成
        ];
    }
}

