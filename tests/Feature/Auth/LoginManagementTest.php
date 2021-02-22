<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @testdox 로그인 관련 테스트
 */
class LoginManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox 사용자는 로그인 할 수 있다.
     */
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

    /**
     * @test
     * @testdox 이메일 인증 사용자만 로그인 할 수 있다.
     */
    public function only_email_authenticated_user_can_login()
    {
        $user = User::factory()->create(array_merge($this->data(), ['activated' => false]));

        $payload = ['email' => $user->email, 'password' => 'password'];
        $this->postJson(route('login.store'), $payload)->assertStatus(401);

        $this->assertGuest();
    }

    /**
     * @test
     * @testdox 가입한 사용자만 로그인 할 수 있다.
     */
    public function must_be_a_register_user()
    {
        User::factory()->create($this->data());

        $payload = ['email' => 'not@register', 'password' => 'what'];
        $this->postJson(route('login.store'), $payload)->assertStatus(401);

        $this->assertGuest();

    }

    /**
     * @test
     * @testdox 사용자는 로그아웃 할 수 있다.
     */
    public function a_user_can_be_logout()
    {
        $token = $this->authenticate();

        $this->withHeaders(['Authorization' => 'Bearer '. $token])
            ->deleteJson(route('login.destroy'))
            ->assertStatus(200);

        $this->assertGuest();
    }

    /**
     * @test
     * @testdox 인증된 사용자만 로그아웃 할 수 있다.
     */
    public function only_authenticated_user_can_logout()
    {
        $this->deleteJson(route('login.destroy'))
            ->assertStatus(401);
    }

    /**
     * @test
     * @testdox 사용자는 로그인 정보를 조회 할 수 있다.
     */
    public function a_user_can_inquire_login_info()
    {
        $token = $this->authenticate();

        $response = $this->withHeaders(['Authorization' => 'Bearer '. $token])
            ->getJson(route('login.index'));

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'data' => true,
        ]);
    }

    /**
     * @test
     * @testdox 인증된 사용자만 로그인 정보를 조회 할 수 있다.
     */
    public function only_authenticated_user_can_inquire_login_info()
    {
        $this->getJson(route('login.index'))
            ->assertStatus(401);
    }

    protected function authenticate()
    {
        $user = User::factory()->create();

        return \JWTAuth::fromUser($user);
    }

    private function data()
    {
        return  [
            'email' => 'test@test.com',
            'password' => \Hash::make('password'),
        ];
    }
}
