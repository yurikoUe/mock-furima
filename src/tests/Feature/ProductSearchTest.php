<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Favorite;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;
    public function test_商品名で部分一致検索ができる()
    {
        // テストデータ作成
        Product::factory()->create(['name' => 'テスト商品A']);
        Product::factory()->create(['name' => 'テスト商品B']);
        Product::factory()->create(['name' => 'サンプルC']);

        // 「テスト」で検索
        $response = $this->get(route('index', ['keyword' => 'テスト']));

        // 期待する商品が表示されていることを確認
        $response->assertSee('テスト商品A')
                ->assertSee('テスト商品B')
                ->assertDontSee('サンプルC');
    }

    public function test_検索状態がマイリストでも保持されている()
    {
        // ユーザー作成 & ログイン
        // ユーザー作成 & ログイン（プロフィールが完了している状態に設定）
        $user = User::factory()->create(['profile_completed' => true]);
        $this->actingAs($user);

        // テストデータ作成
        $product1 = Product::factory()->create(['name' => 'テスト商品A']);
        Product::factory()->create(['name' => 'テスト商品B']);
        Product::factory()->create(['name' => 'サンプルC']);

        $response = $this->get(route('index', ['keyword' => 'テスト']));
        $response->assertStatus(200);

        $response->assertSee('テスト商品A');
        
        Favorite::create(['user_id' => $user->id, 'product_id' => $product1->id]);

        // 検索後にマイリストへ遷移
        $response = $this->get('/?tab=mylist', ['keyword' => 'テスト', 'tab' => 'mylist']);
        $response->assertStatus(200);

        // 期待する商品が表示され、検索キーワードがビューに渡されていることを確認
        $response->assertSee('テスト商品A');
    }
}
