<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store()
    {
        if (! $token = $this->guard()->attempt(request()->all())) {
            return response()->json($this->errorJson(), 401);
        }

        $user = Auth::user();
        if (! $user->activated) {
            $this->guard()->logout();
            return response()->json(array_merge($this->errorJson(),
                ['message' => '메일함에서 본인인증 해주세요.']), 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => '입력하신 메일함에서 인증확인 메일을 확인해주세요.',
            'data'    => $user,
        ], 200)->header('Authorization', $token);
    }

    public function destroy()
    {
        $this->guard()->logout();

        return response()->json([
            'status' => 'success',
            'message' => '로그아웃 성공했습니다',
            'data'    => Auth::user(),
        ], 200);
    }

    private function errorJson()
    {
        return [
            'status' => 'unauthorized',
            'message' => '입력을 다시 확인해주세요.',
            'error' => '401'
        ];
    }

    private function guard()
    {
        return Auth::guard();
    }
}
