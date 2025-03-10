<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->sentence,
        ];
    }
}
