<?php

namespace App\Services;

use App\Contracts\Services\AuthContract;
use App\Helpers\UtilityHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthService implements AuthContract
{

    public function __construct()
    {
        //
    }

    public function login($request, $guard)
    {
        try{
            $credentials = $request->only(['email', 'password']);
            if (! $token = Auth::guard($guard)->attempt($credentials)) {
                return UtilityHelper::RETURN_ERROR_FORMAT(
                    ResponseAlias::HTTP_UNAUTHORIZED,
                    'Unauthorized Access!',
                );
            }

            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK,
                'Successfully Authenticated!',
                $this->respondWithToken($token, $guard)
            );
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    protected function respondWithToken($token, $guard)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => Auth::guard($guard)->user(),
            'expires_in' => Auth::guard($guard)->factory()->getTTL() * 60 * 24
        ];
    }
}
