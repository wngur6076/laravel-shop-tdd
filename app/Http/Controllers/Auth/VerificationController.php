<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Traits\AuthTrait;
use App\Http\Controllers\Controller;

class VerificationController extends Controller
{
    use AuthTrait;

    public function store()
    {
        $user = User::whereConfirmCode(request('code'))->first();

        if(! $user) {
            return response()->json([
                'status' => 'code_denied',
                'message' => '존재하지 않는 코드입니다.',
                'error' => '401'
            ], 401);
        }

        $user->activated = true;
        $user->confirm_code = null;
        $user->save();

        $token = $this->guard()->login($user);

        return response()->json(array_merge($this->successJson($user),
            ['message' => '로그인 성공했습니다.']), 200)
            ->header('Authorization', $token);
    }
}
