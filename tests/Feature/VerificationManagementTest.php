<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @testdox 가입인증 관련 테스트
 */
class VerificationManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox 사용자는 가입인증 할 수 있다.
     */
    public function a_user_can_be_authenticated()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['confirm_code' => \Str::random(60), 'activated' => false]);

        $this->assertFalse($user->activated);
        $response = $this->postJson('/api/auth/verification', ['code' => $user->confirm_code]);
        $this->assertTrue($user->fresh()->activated);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => true,
            ]);
    }

    /**
     * @test
     * @testdox 유효한 인증 코드이어야 한다.
     */
    public function must_be_a_valid_verification_code()
    {
        $response = $this->postJson('/api/auth/verification', ['code' => \Str::random(60)]);
        $response->assertStatus(401)
            ->assertJson([
                'status' => 'code_denied',
                'error' => 401,
            ]);
    }
}
