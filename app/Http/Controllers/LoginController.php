<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store()
    {
        if (! $token = $this->guard()->attempt(request()->all())) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '입력을 다시 확인해주세요.',
                'error' => '401'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => '입력하신 메일함에서 인증확인 메일을 확인해주세요.',
            'data'    => 's',
        ], 200)->header('Authorization', $token);
    }

    private function guard()
    {
        return Auth::guard();
    }
}
