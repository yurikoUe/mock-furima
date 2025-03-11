<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 変更項目が初期値として過去設定されている()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'postal_code' => '123-4567',
            'address' => '123 Test St.',
            'building' => 'Test Building',
            'profile_image' => 'profile_images/test-image.jpg',
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);

        // ユーザーにログイン
        $this->actingAs($user);

        // プロフィール編集ページにアクセス
        $response = $this->get(route('mypage.profile'));
        $response->assertStatus(200);

        // 初期値が正しく表示されるか確認
        $response->assertSee($user->name);  // ユーザー名
        $response->assertSee($user->postal_code);  // 郵便番号
        $response->assertSee($user->address);  // 住所
        $response->assertSee($user->building);  // 建物名
        $response->assertSee(asset('storage/' . $user->profile_image));  // プロフィール画像
    }
}
