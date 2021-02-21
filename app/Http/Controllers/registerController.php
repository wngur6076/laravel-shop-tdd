<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\RegisterConfirmMail;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store()
    {
        $data = $this->vadateRequest();
        $user = User::create(array_merge($data, [
            'password' => Hash::make($data['password']),
            'confirm_code' => \Str::random(60),
        ]));

         // 가입확인 메일
        \Mail::to($user)->send(
            new RegisterConfirmMail(config('app.url') . '/auth/confirm/?code=' . $user->confirm_code)
        );

        return response()->json([
            'status' => 'success',
            'message' => '입력하신 메일함에서 인증확인 메일을 확인해주세요.',
            'data'    => $user,
        ], 201);
    }

    protected function vadateRequest()
    {
        return request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
    }
}
