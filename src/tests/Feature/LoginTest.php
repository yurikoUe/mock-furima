<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // １. メールアドレスが入力されていない場合のテスト
    public function test_email_is_required()
    {
        $response = $this->withoutMiddleware()->post('/login', [
            'email' => '', // メールアドレス空
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = $response->getSession()->get('errors')->get('email');
        $this->assertContains('メールアドレスを入力してください', $errors);
    }

    // ２. パスワードが入力されていない場合のテスト
    public function test_password_is_required()
    {
        $response = $this->withoutMiddleware()->post('/login', [
            'email' => 'user@example.com',
            'password' => '', // パスワード空
        ]);

        $response->assertSessionHasErrors('password');
        $response->assertSessionHas('errors');
        $errors = $response->getSession()->get('errors')->get('password');
        $this->assertContains('パスワードを入力してください', $errors);
    }

    // ３. 入力情報が間違っている場合のテスト
    public function test_invalid_login()
    {
        // 仮のユーザーを作成
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->withoutMiddleware()->post('/login', [
            'email' => 'wrongemail@example.com', // 存在しないメールアドレス
            'password' => 'wrongpassword', // 誤ったパスワード
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertSessionHas('errors');
        $errors = $response->getSession()->get('errors')->get('email');
        $this->assertContains('ログイン情報が登録されていません', $errors);
    }

    // ４. 正しい情報が入力された場合のテスト
    public function test_successful_login()
    {
        // 仮のユーザーを作成
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->withoutMiddleware()->actingAs($user)->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/'); 
        $this->assertAuthenticatedAs($user); 
    }
}
