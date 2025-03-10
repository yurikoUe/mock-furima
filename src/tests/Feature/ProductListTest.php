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


class ProductListTest extends TestCase
{
    use RefreshDatabase;

    /** test1: 全商品を取得できるか */
    public function test_all_products_are_displayed()
    {
        $this->seed(CategorySeeder::class);
        Product::factory()->count(3)->create();

        // 商品一覧ページへアクセス
        $response = $this->get('/');

        // ステータスコード200を確認
        $response->assertStatus(200);

        // 商品がすべて表示されることを確認
        $products = Product::all();
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

     /** test2: 購入済み商品は「Sold」と表示されるか */
    public function test_sold_label_is_displayed_for_purchased_products()
    {
        $this->seed(CategorySeeder::class);
        $product = Product::factory()->create();
        
        // 購入済みの注文を作成
        Order::factory()->create([ 'product_id' => $product->id ]);
        
        // 商品一覧ページにアクセス
        $response = $this->get('/');
        
        // 「SOLD」が表示されていることを確認
        $response->assertSee('Sold');
    }

    /** test3: 自分が出品した商品は表示されないか */
    public function test_user_does_not_see_their_own_products()
    {
        $this->seed(CategorySeeder::class);
        $user = User::factory()->create();
        $this->actingAs($user); //ログインしたユーザーとしてテスト
        
        // そのユーザーの商品を作成（ProductFactory.phpにてconfigureメソッドでカテゴリーを自動で関連付け）
        $product = Product::factory()->create(['user_id' => $user->id]);
        
        // 商品一覧ページにアクセス
        $response = $this->get('/');
        
        // 自分の商品が表示されていないことを確認
        $response->assertDontSee($product->name);
    }

}
