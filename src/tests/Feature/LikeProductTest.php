<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class LikeProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコンを押下することによって、いいねした商品として登録できる()
    {
        // ダミーのユーザー、商品、コメントなどを作成
        $user = User::factory()->create(['email_verified_at' => now()]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ログイン
        $this->actingAs($user);

        // 商品詳細ページにアクセス
        $response = $this->get(route('product.show', $product->id));

        // いいねアイコンをクリックして商品をいいね
        $response = $this->post(route('favorite.store', $product->id));

        // 商品が「お気に入り」に登録されているか確認
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // いいね合計数が増加しているか確認
        $product->refresh();
        $this->assertEquals(1, $product->favoritedBy()->count());
    }

    public function test_追加済みのアイコンは色が変化する()
    {
        // ダミーのユーザー、商品、コメントなどを作成
        $user = User::factory()->create(['email_verified_at' => now()]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ログイン
        $this->actingAs($user);

        // 商品詳細ページにアクセス
        $response = $this->get(route('product.show', $product->id));

        // いいね前のアイコンが期待通りであることを確認
        $response->assertSee('star.svg');

        // いいねアイコンをクリックしてお気に入り登録
        $response = $this->post(route('favorite.store', $product->id));

        // リダイレクト先にアクセスして、アイコンが変わったことを確認
        $response = $this->get(route('product.show', $product->id));
        $response->assertSee('gold-star.svg');
    }

    public function test_再度いいねアイコンを謳歌することによって、いいねを解除できる()
    {
        // ダミーのユーザー、商品、コメントなどを作成
        $user = User::factory()->create(['email_verified_at' => now()]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ログイン
        $this->actingAs($user);

        // 商品詳細ページにアクセス
        $response = $this->get(route('product.show', $product->id));

        // いいねを最初に登録
        $this->post(route('favorite.store', $product->id));

        // 商品が「お気に入り」に登録されているか確認
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // いいねを解除
        $response = $this->post(route('favorite.destroy', $product->id));

        // 商品が「お気に入り」から解除されていることを確認
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        // いいね合計が0に戻っていることを確認
        $product->refresh();
        $this->assertEquals(0, $product->favoritedBy()->count());
    }

}
