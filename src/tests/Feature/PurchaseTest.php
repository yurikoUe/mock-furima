<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\OrderAddress;
use Mockery;
use Illuminate\Support\Facades\Http;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();
        
        // モック作成の前に、Sessionクラスを適切にモックする
        $this->mockSession = Mockery::mock('alias:\Stripe\Checkout\Session');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        
        // モックのリセット
        Mockery::close();
    }

    /** @test */
    public function test_「購入する」ボタンを押下すると購入が完了する()
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

        // 住所を更新
        $orderAddress = OrderAddress::create([
            'user_id' => $user->id,
            'order_postal_code' => '123-4567',
            'order_address' => '東京都渋谷区',
            'order_building' => '渋谷ビル',
        ]);

        // モックを使ってSession::retrieveメソッドをシミュレート
        $this->mockSession->shouldReceive('retrieve')
            ->once()
            ->andReturn((object)[
                'payment_status' => 'paid',
                'metadata' => (object)['order_id' => 1],
            ]);

        // 商品購入画面を表示
        $response = $this->get(route('purchase.create', ['item_id' => $product->id]));

        // 商品購入フォームに送信
        $response = $this->post(route('checkout'), [
            'product_id' => $product->id,
            'payment_method' => 'card',
            'order_address_id' => $orderAddress->id,
        ]);

        // 注文を実際に作成
        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => 'pending',
            'order_address_id' => $orderAddress->id,
        ]);

        // 購入ステータスが「決済完了」になっていることを確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => '決済完了',
            'order_address_id' => $orderAddress->id,
        ]);
    }

    //** @test2 */
    public function test_購入した商品は商品一覧画面でSoldと表示される()
    {
        // 出品者を作成
        $seller = User::factory()->create();

        // ダミーのユーザー、商品、コメントなどを作成してログイン
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);
        $this->actingAs($user);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 出品者が商品を作成
        $product = Product::factory()->create(['user_id' => $seller->id]);
        $product->categories()->attach($category);

        // 購入済みの注文を作成
        Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => '決済完了',
        ]);

        // 商品一覧ページを表示
        $response = $this->get(route('index'));

        // SOLD の表示を確認
        $response->assertSee('Sold');
    }

    /** @test3 */
    public function test_「プロフィール／購入した商品一覧」に追加されている()
    {
        // カテゴリーを生成して商品h情報作成
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ユーザーを作成してログイン
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => true, // 住所登録済みとする
        ]);
        $this->actingAs($user);

        // 購入済みの注文を作成
        Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'status' => '決済完了',
        ]);

        // プロフィールを表示（購入タブを開く）
        $response = $this->get(route('mypage', ['tab' => 'buy']));

        // 商品名が表示されていることを確認
        $response->assertSee($product->name);
    }
}
