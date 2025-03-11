<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が取得出来る()
    {
        // ダミーのユーザー、商品、コメントなどを作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // 出品した商品と購入した商品を作成
        $sellingProduct = Product::factory()->create(['user_id' => $user->id]);
        $purchasedProduct = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => Product::factory()->create()->id,
        ]);

        // プロフィールページを開く
        $response = $this->get(route('mypage'));

        // ステータスコード200が返されることを確認
        $response->assertStatus(200);

        // プロフィール画像が表示されるか確認
        $profileImagePath = $user->profile_image ? 'storage/' . $user->profile_image : 'storage/images/default-profile-image.png';
        $response->assertSee($profileImagePath);

        // ユーザ名が表示されるか確認
        $response->assertSee($user->name);

        // 出品した商品が表示されるか確認
        $response->assertSee($sellingProduct->name);

        // 購入した商品タブを選択
        $response = $this->get(route('mypage') . '?tab=buy');
        $response->assertStatus(200);

        // 購入した商品が表示されるか確認
        $response->assertSee($purchasedProduct->product->name);
    }
}
