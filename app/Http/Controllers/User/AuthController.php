<?php

namespace App\Http\Controllers\User;

use App\Contracts\Services\AuthContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    private $authService;
    protected $guard = 'user';

    public function __construct(AuthContract $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $serviceResponse = $this->authService->login($request, $this->guard);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

}
