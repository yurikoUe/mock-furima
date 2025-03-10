<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト：ログアウトができる
     *
     * @return void
     */
    public function test_logout_can_be_done()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->withoutMiddleware()->post(route('logout'));

        $this->assertGuest();
    }
}
