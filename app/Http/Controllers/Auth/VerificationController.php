<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Traits\AuthTrait;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;

class VerificationController extends Controller
{
    use AuthTrait;

    public function __invoke()
    {
        try {
            $user = User::whereConfirmCode(request('code'))->firstOrFail();
            // 유저한테 활성화 요청한다.
            $user->activation();
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'code_denied',
                'message' => '존재하지 않는 코드입니다.',
                'error' => '404'
            ], 404);
        }

        $token = $this->guard()->login($user);

        return response()->json(array_merge($this->successJson($user),
            ['message' => '로그인 성공했습니다.']), 200)
            ->header('Authorization', $token);
    }
}
