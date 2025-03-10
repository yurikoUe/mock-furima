<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    // １. 名前が入力されていない場合のテスト
    public function test_validation_error_when_name_is_empty()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $errors = $response->getSession()->get('errors')->get('name');
        $this->assertContains('お名前を入力してください', $errors);
    }

    // ２. メールアドレスが入力されていない場合のテスト
    public function test_validation_error_when_email_is_empty()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $errors = $response->getSession()->get('errors')->get('email');
        $this->assertContains('メールアドレスを入力してください', $errors);
    }

    // ３. パスワードが入力されていない場合のテスト
    public function test_validation_error_when_password_is_empty()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $errors = $response->getSession()->get('errors')->get('password');
        $this->assertContains('パスワードを入力してください', $errors);
    }

    // ４.パスワードが7文字以下の場合のテスト
    public function test_validation_error_when_password_is_7_characters_or_less()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'shorter',
            'password_confirmation' => 'shorter',
        ]);

        $response->assertSessionHasErrors('password');
        $errors = $response->getSession()->get('errors')->get('password');
        $this->assertContains('パスワードは8文字以上で入力してください。', $errors);
    }

    // ５. パスワードが確認用パスワードと一致しない場合のテスト
    public function test_validation_error_when_passwords_do_not_match()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword', // 確認用パスワードが異なる
        ]);

        $response->assertSessionHasErrors('password_confirmation'); 
        $errors = $response->getSession()->get('errors')->get('password_confirmation');
        $this->assertContains('パスワードと一致しません', $errors); // エラーメッセージが一致することを確認
    }

    // 6.正しい情報が入力された場合のテスト
    public function test_validation_success_when_all_fields_are_correctly_filled()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/login'); // ログイン画面に遷移することを確認
    }

}
