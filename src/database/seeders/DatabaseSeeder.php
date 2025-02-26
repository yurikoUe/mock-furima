<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // brandsを先にシーディング
        $this->call(BrandSeeder::class);

        // conditionを先にシーディング
        $this->call(ConditionSeeder::class);

        // カテゴリー（categories）を次にシーディング
        $this->call(CategorySeeder::class);

        // ユーザーをファクトリで作成（10人分）
        User::factory(10)->create();

        // 商品をシーディング（user_id, brand_id を利用）
        $this->call(ProductSeeder::class);
    }
}
