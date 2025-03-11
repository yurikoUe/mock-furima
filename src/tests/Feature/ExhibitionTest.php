<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\UploadedFile;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_出品商品情報が正しく保存される()
    {
        // ダミーのユーザー、商品、コメントなどを作成してログイン
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);
        $this->actingAs($user);
        
        // テスト用カテゴリとブランド作成
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        
        // テスト用画像ファイル作成
        $image = UploadedFile::fake()->create('test.jpg', 100);
        
        // フォーム送信データ
        $data = [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'image' => $image,
            'category_id' => [$category->id],
            'condition' => '良好',
            'price' => 1000,
            'brand' => $brand->id,
        ];
        
        // 出品処理を実行
        $response = $this->actingAs($user)->post(route('sell.store'), $data);
        
        // データが保存されていることを確認
        $this->assertDatabaseHas('products', [
            'name' => 'テスト商品',
            'description' => 'これはテスト用の商品です。',
            'price' => 1000,
            'condition' => '良好',
            'brand_id' => $brand->id,
            'user_id' => $user->id,
        ]);
        
        // カテゴリーとの関連付け確認
        $product = Product::where('name', 'テスト商品')->first();
        $this->assertTrue($product->categories->contains($category->id));
    
    }
}
