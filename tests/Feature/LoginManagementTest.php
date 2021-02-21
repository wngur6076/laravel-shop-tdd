<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_login()
    {
        $user = User::factory()->create($this->data());

        // id/pw 입력 후 로그인 한다.
        $payload = ['email' => $user->email, 'password' => 'password'];
        $response = $this->postJson(route('login.store'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => true,
            ])
            ->assertHeader('Authorization');

        $this->assertAuthenticated();
    }

    /** @test */
    public function only_email_authenticated_user_can_login()
    {
        $user = User::factory()->create(array_merge($this->data(), ['activated' => false]));

        // id/pw 입력 후 로그인 한다.
        $payload = ['email' => $user->email, 'password' => 'password'];
        $this->postJson(route('login.store'), $payload)->assertStatus(401);

        $this->assertGuest();
    }

    /** @test */
    public function must_be_a_register_user()
    {
        User::factory()->create($this->data());

        $payload = ['email' => 'not@register', 'password' => 'what'];
        $this->postJson(route('login.store'), $payload)->assertStatus(401);

        $this->assertGuest();

    }

    /** @test */
    public function a_user_can_be_logout()
    {
        $user = User::factory()->create($this->data());
        $this->actingAs($user);
        $token = \JWTAuth::fromUser($user);

        $this->deleteJson(route('login.destroy'), [], ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(200);

        $this->assertGuest();
    }

    private function data()
    {
        return  [
            'email' => 'test@test.com',
            'password' => \Hash::make('password'),
        ];
    }
}
