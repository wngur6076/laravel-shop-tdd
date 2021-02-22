<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use AspectMock\Test as test;

/**
 * @testdox 토큰 재발급 관련 테스트
 */
class RefreshManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox 사용자는 토큰 재발급 할 수 있다.
     */
    public function a_user_can_be_token_refres()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer '. $token])
            ->getJson(route('refresh'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => true,
            ])
            ->assertHeader('Authorization');

        $this->assertAuthenticated();
    }

    /**
     * @test
     * @testdox 인증 된 사용자만 토큰 재발급 할 수 있다.
     */
    public function only_authenticated_user_can_token_refres()
    {
        $this->getJson(route('refresh'))
            ->assertStatus(401);
    }

    protected function authenticate()
    {
        $user = User::factory()->create();

        return \JWTAuth::fromUser($user);
    }
}
