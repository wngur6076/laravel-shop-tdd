<?php
namespace App\Traits;

trait AuthTrait
{
    private function successJson($data)
    {
        return [
            'status' => 'success',
            'message' => '입력하신 메일함에서 인증확인 메일을 확인해주세요.',
            'data'    => $data,
        ];
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
        return auth()->guard();
    }
}
