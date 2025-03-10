<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use App\Models\Comment;
use App\Models\Brand;
use App\Models\Product;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細ページに必要な情報が表示されることを確認()
    {
        // ダミーのユーザー、商品、コメントなどを作成
        $user = User::factory()->create();

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);
        $product->categories()->attach($category);

        // 商品にコメントを追加
        $comment = Comment::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'content' => '素晴らしい商品！',
        ]);

        // 商品詳細ページにアクセス
        $response = $this->get('/item/' . $product->id);

        // レスポンスが正しく表示されるか確認
        $response->assertStatus(200);
        $response->assertSee($product->name); // 商品名が表示されている
        $response->assertSee("¥" . number_format($product->price) . "(税込)"); // 価格が表示されている
        $response->assertSee($product->brand->name); // ブランド名が表示されている
        $response->assertSee($product->categories->first()->name); // カテゴリ名が表示されている
        $response->assertSee($product->description); // 商品説明が表示されている
        $response->assertSee($comment->content); // コメント内容が表示されている
        $response->assertSee($comment->user->name); // コメントしたユーザーの名前が表示されている

        // 画像や状態、いいね数などの表示チェック（これらは別途データを作成してチェック）
        $response->assertSee($product->image); // 商品画像が表示されている
        $response->assertSee($product->status); // 商品の状態が表示されている
        $response->assertSee($product->favoritedBy()->count()); // いいね数が表示されている
    }
}
