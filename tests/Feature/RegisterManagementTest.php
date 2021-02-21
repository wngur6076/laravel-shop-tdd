<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Mail\RegisterConfirmMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_created()
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

    /** @test */
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

    /** @test */
    public function a_password_is_min_number()
    {
        $testPassword = ['1', '12', '123', '1234',
            '12345', '123456', '1234567'];
        $response = $this->post(route('register.store'),
            array_merge($this->data(), ['password' => $testPassword[mt_rand(0, 6)]]));
        $response->assertSessionHasErrors('password');
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function a_email_must_be_email_format()
    {
        $response = $this->post(route('register.store'),
            array_merge($this->data(), ['email' => 'test']));
        $response->assertSessionHasErrors('email');
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function a_email_is_unique()
    {
        $this->post(route('register.store'), $this->data(),  ['email' => 'test@test.com']);
        $response = $this->post(route('register.store'), $this->data(),  ['email' => 'test@test.com']);
        $response->assertSessionHasErrors('email');
        $this->assertCount(1, User::all());
    }

    /** @test */
    public function a_confirm_code_is_not_null()
    {
        $this->post(route('register.store'), $this->data());
        $user = User::first();
        $this->assertNotNull($user->confirm_code);
    }

    /** @test */
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
