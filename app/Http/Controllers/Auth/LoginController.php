<?php

namespace App\Http\Controllers\Auth;

use App\Traits\AuthTrait;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    use AuthTrait;

    public function store()
    {
        if (! $token = $this->guard()->attempt(request()->all())) {
            return response()->json($this->errorJson(), 401);
        }

        $user = auth()->user();
        if (! $user->activated) {
            $this->guard()->logout();
            return response()->json(array_merge($this->errorJson(),
                ['message' => '메일함에서 본인인증 해주세요.']), 401);
        }

        return response()->json($this->successJson($user), 200)->header('Authorization', $token);
    }

    public function destroy()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'message' => '로그아웃 성공했습니다',
            'data'    => auth()->user(),
        ], 200);
    }
}
