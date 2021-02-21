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
    public function a_guest_can_be_login()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create($this->data());

        // id/pw 입력 후 로그인 한다.
        $payload = ['email' => $user->email, 'password' => 'password'];
        $response = $this->postJson('/api/auth/login', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => true,
            ])
            ->assertHeader('Authorization');

        $this->assertAuthenticated();
    }

    private function data()
    {
        return  [
            'email' => 'test@test.com',
            'password' => \Hash::make('password'),
        ];
    }
}
