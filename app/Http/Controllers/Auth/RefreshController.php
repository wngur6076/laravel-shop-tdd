<?php

namespace App\Http\Controllers\Auth;

use App\Traits\AuthTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefreshController extends Controller
{
    use AuthTrait;

    public function __invoke()
    {
        if (! $token = $this->guard()->refresh()) {
            return array_merge($this->errorJson(), ['message' => '토큰 재발급 실패했습니다.']);
        }

        return response()->json(array_merge($this->successJson(auth()->user()),
            ['message' => '토큰 재발급 성공했습니다.']), 200)
            ->header('Authorization', $token);
    }
}
