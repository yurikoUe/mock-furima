<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderAddress;
use App\Models\Category;

class DeliveryAddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 送付先住所変更画面で登録した住所が商品購入画面に反映される()
    {
        // ダミーのユーザー、商品、コメントなどを作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ログイン
        $this->actingAs($user);

        // 送付先住所変更画面にアクセス
        $response = $this->get(route('mypage.profile'));
        $response->assertStatus(200);

        // 住所を更新
        $orderAddress = OrderAddress::create([
            'user_id' => $user->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都渋谷区',
            'order_building' => '渋谷ビル',
        ]);

        // 商品購入ページにアクセス
        $response = $this->get(route('purchase.create', ['item_id' => $product->id]));

        // 登録した住所がページに表示されていることを確認
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区');
        $response->assertSee('渋谷ビル');
    }

    /** @test */
    public function 購入した商品に送付先住所が紐づいて登録される()
    {
        // ダミーのユーザー、商品、コメントなどを作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ログイン
        $this->actingAs($user);

        // 送付先住所変更画面にアクセス
        $response = $this->get(route('mypage.profile'));
        $response->assertStatus(200);

        // 住所を更新
        $orderAddress = OrderAddress::create([
            'user_id' => $user->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都渋谷区',
            'order_building' => '渋谷ビル',
        ]);

        // 商品購入フォームに送信
        $response = $this->post(route('checkout'), [
            'product_id' => $product->id,
            'payment_method' => 'card', // 適切な支払い方法を選択
            'order_address_id' => $orderAddress->id,
        ]);

        // 購入後、注文に住所が紐づいていることを確認
        $order = Order::where('product_id', $product->id)->first();
        $this->assertEquals($orderAddress->id, $order->order_address_id);
    }

}
