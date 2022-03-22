<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use  App\Http\Controllers\Controller;
use App\Contracts\Services\UserContract;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserContract $userService)
    {
        $this->userService = $userService;

    }
    public function getAllUsers()
    {
        $serviceResponse = $this->userService->getAllUsers();
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function createUser(Request $request)
    {
        $serviceResponse = $this->userService->createUser($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function editUser($id)
    {
        return $this->userService->editUser($id);
    }

    public function updateUser(Request $request)
    {
        $serviceResponse = $this->userService->updateUser($request);
        return response()->json('User Updated successfully!', $serviceResponse['status']);


    }

    public function deleteUser($id)
    {
        $serviceResponse = $this->userService->deleteUser( (int) $id);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }



}
