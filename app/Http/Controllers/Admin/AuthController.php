<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\AuthContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthContract $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        return response()->json($this->authService->login($request), $this->authService->login($request)['status']);
    }

}
