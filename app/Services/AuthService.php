<?php

namespace App\Services;

use App\Contracts\Services\AuthContract;
use App\Helpers\UtilityHelper;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthService implements AuthContract
{

    public function __construct()
    {
        //
    }

    public function login($request)
    {
        try{
            $credentials = $request->only(['email', 'password']);
            if (! $token = Auth::attempt($credentials)) {
                return UtilityHelper::RETURN_ERROR_FORMAT(
                    ResponseAlias::HTTP_UNAUTHORIZED,
                    'Unauthorized Access!',
                );
            }

            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK,
                'User Successfully Authenticated!',
                $this->respondWithToken($token)
            );
        }catch (Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => auth()->user()
        ];
    }
}
