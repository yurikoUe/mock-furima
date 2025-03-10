<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class CommentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みのユーザーはコメントを送信できる()
    {
        // テスト用ユーザーと商品を作成
        $user = User::factory()->create(['email_verified_at' => now()]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);


        // ユーザーでログイン
        $this->actingAs($user);

        // コメントを送信
        $response = $this->post(route('product.comment.store', $product->id), [
            'comment' => '素晴らしい商品です！'
        ]);

        // コメントが保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'content' => '素晴らしい商品です！'
        ]);

        // 商品詳細ページにリダイレクトされることを確認
        $response->assertRedirect(route('product.show', $product->id));

        // コメント数が増えていることを確認
        $this->assertEquals($product->comments()->count(), 1);
    }

    public function test_ログイン前のユーザーはコメントを送信できない()
    {
        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ゲストユーザー（未ログイン）でコメントを送信
        $response = $this->post(route('product.comment.store', $product->id), [
            'comment' => '素晴らしい商品です！'
        ]);

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect(route('login'));
    }

    public function test_コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        // テスト用ユーザーと商品を作成
        $user = User::factory()->create(['email_verified_at' => now()]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ユーザーでログイン
        $this->actingAs($user);

        // 空のコメントを送信
        $response = $this->post(route('product.comment.store', $product->id), [
            'comment' => ''
        ]);

        // リダイレクト後にエラーメッセージがセッションに格納されていることを確認
        $response->assertRedirect();
        $response->assertSessionHasErrors('comment');
        $this->assertEquals(session('errors')->get('comment')[0], 'コメントを入力してください。');
    }

    public function test_コメントが255文字以上の場合、バリデーションメッセージが表示される()
    {
        // テスト用ユーザーと商品を作成
        $user = User::factory()->create(['email_verified_at' => now()]);

        // カテゴリーをファクトリーで生成
        $category = Category::factory()->create();

        // 商品情報作成
        $product = Product::factory()->create();
        $product->categories()->attach($category);

        // ユーザーでログイン
        $this->actingAs($user);

        // 256文字のコメントを送信
        $response = $this->post(route('product.comment.store', $product->id), [
            'comment' => str_repeat('a', 256)
        ]);

        // リダイレクト後にエラーメッセージがセッションに格納されていることを確認
        $response->assertRedirect();
        $response->assertSessionHasErrors('comment');
        $this->assertEquals(session('errors')->get('comment')[0], 'コメントは255文字以内で入力してください。');
    }

}
