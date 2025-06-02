<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 出品者A（指定データで作成）
        User::factory()->create([
            'name' => 'ユーザーA',
            'email' => 'usera@example.com',
            'profile_image' => 'images/usera.png',
        ]);

        // 出品者B（指定データで作成）
        User::factory()->create([
            'name' => 'ユーザーB',
            'email' => 'userb@example.com',
            'profile_image' => 'images/userb.png',
        ]);

        // 商品を出品していないユーザーC（指定データで作成）
        User::factory()->create([
            'name' => 'ユーザーC',
            'email' => 'userc@example.com',
            'profile_image' => 'images/userc.png',
        ]);
    }
}
