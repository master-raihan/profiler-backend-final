<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\ContactContract;
use Illuminate\Http\Request;
use  App\Http\Controllers\Controller;
use App\Contracts\Services\UserContract;

class UserController extends Controller
{
    private $userService;
    private $contactService;

    public function __construct(UserContract $userService, ContactContract $contactService)
    {
        $this->userService = $userService;
        $this->contactService = $contactService;
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

    public function getContactsByUser(Request $request)
    {
        $serviceResponse = $this->contactService->getAllContactsByUser($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

}
