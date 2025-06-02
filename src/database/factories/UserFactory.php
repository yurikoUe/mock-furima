<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // 全員共通のパスワード（後でログイン確認用）
            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'building' => $this->faker->secondaryAddress(),
            'profile_image' => 'images/default.png',
            'email_verified_at' => now(), // デフォルト認証済みに
            'profile_completed' => true, // デフォルト完了済みに
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
