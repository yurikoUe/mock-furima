<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Database\Seeders\CategorySeeder;

class MyListFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_いいねした商品だけが表示されるか()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'profile_completed' => true, // プロフィールが登録済みの状態にする
        ]);
        $this->seed(CategorySeeder::class);
        $product1 = Product::factory()->create(['name' => 'テスト商品1']);
        $product2 = Product::factory()->create(['name' => 'テスト商品2']);

        // ユーザーが商品1をいいね
        $user->favorites()->attach($product1);
        
        // ログイン
        $this->actingAs($user);

        // マイリストページを訪問
        $response = $this->get('/?tab=mylist');

        // 商品1は表示され、商品2は表示されない
        $response->assertSee($product1->name);
        $response->assertDontSee($product2->name);
    }

    public function test_購入済みの商品は「Sold」と表示されるか()
    {
        // ユーザーを作成
        $user = User::factory()->create([
            'profile_completed' => true, // プロフィールが登録済みの状態にする
        ]);
        $this->seed(CategorySeeder::class);
        $product = Product::factory()->create();

        $user->favorites()->attach($product);

        // ログイン
        $this->actingAs($user);

        // 商品を購入し、決済完了にする
        $order = Order::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'status' => '決済完了',
        ]);

        // マイリストページを訪問
        $response = $this->get('/?tab=mylist');

        // 商品が「Sold」と表示されることを確認
        $response->assertSee('Sold');
        $response->assertSee($product->name);
    }

    public function test_自分が出品した商品は表示されないか()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create([
            'profile_completed' => true, // プロフィールが登録済みの状態にする
        ]);
        $this->seed(CategorySeeder::class);
        $product = Product::factory()->create([
            'user_id' => $user->id, // ユーザーが出品した商品
        ]);

        // ログイン
        $this->actingAs($user);

        // マイリストページを訪問
        $response = $this->get('/?tab=mylist');

        // 出品した商品は表示されない
        $response->assertDontSeeText($product->name);
    }

    public function test_未承認の場合は何も表示されない()
    {
        // プロフィール未完了のユーザーを作成
        // $user = User::factory()->create([
        //     'profile_completed' => false,
        // ]);

        $user = User::factory()->unverified()->create(); // メール未認証状態

        // ログイン
        $this->actingAs($user);

        // マイリストページを訪問
        $response = $this->get('/?tab=mylist');

        // リダイレクトされることを確認（プロフィールページに）
        $response->assertRedirect(route('mypage.profile'));
    }

}
