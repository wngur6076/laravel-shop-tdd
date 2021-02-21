<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Mail\RegisterConfirmMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @testdox 회원가입 관련 테스트
 */
class RegisterManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @testdox 사용자 회원가입 할 수 있다.
     */
    public function a_user_can_be_register()
    {
        $response = $this->postJson(route('register.store'), $this->data());

        $user = User::first();
        $this->assertCount(1, User::all());
        $this->assertEquals('테스트', $user->name);
        $this->assertEquals('test@test.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data' => true,
            ]);
    }

    /**
     * @test
     * @testdox 필드들은 반드시 입력해야 한다.
     */
    public function fields_are_required()
    {
        collect(['name', 'email', 'password'])
            ->each(function ($field) {
                $response = $this->post(route('register.store'),
                    array_merge($this->data(), [$field => '']));

                $response->assertSessionHasErrors($field);
                $this->assertCount(0, User::all());
            });
    }

    /**
     * @test
     * @testdox 이메일 필드는 이메일 형식이어야 한다.
     */
    public function an_email_must_be_email_format()
    {
        $response = $this->post(route('register.store'),
            array_merge($this->data(), ['email' => 'test']));
        $response->assertSessionHasErrors('email');
        $this->assertCount(0, User::all());
    }

    /**
     * @test
     * @testdox 이메일 필드는 유니크해야 한다.
     */
    public function an_email_is_unique()
    {
        $this->post(route('register.store'), $this->data(),  ['email' => 'test@test.com']);
        $response = $this->post(route('register.store'), $this->data(),  ['email' => 'test@test.com']);
        $response->assertSessionHasErrors('email');
        $this->assertCount(1, User::all());
    }

    /**
     * @test
     * @testdox 가입인증 코드가 null이 아니다.
     */
    public function a_confirm_code_is_not_null()
    {
        $this->post(route('register.store'), $this->data());
        $user = User::first();
        $this->assertNotNull($user->confirm_code);
    }

    /**
     * @test
     * @testdox 가입인증 이메일을 보낼 수 있다.
     */
    public function register_email_can_be_sent()
    {
        Mail::fake();

        $this->post(route('register.store'), $this->data());
        $user = User::first();

        Mail::assertSent(RegisterConfirmMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    private function data()
    {
        return  [
            'name' => '테스트',
            'email' => 'test@test.com',
            'password' => 'password'
        ];
    }
}
