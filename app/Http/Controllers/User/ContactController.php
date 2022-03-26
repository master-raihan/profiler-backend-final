<?php

namespace App\Http\Controllers\User;

use App\Contracts\Services\ContactContract;
use  App\Http\Controllers\Controller;

class ContactController extends Controller
{

    private $contactService;

    public function __construct(ContactContract $contactService)
    {

        $this->contactService = $contactService;

    }

    public function getContactsByUser()
    {
        $serviceResponse = $this->contactService->getAllContactsByAuthUser();
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

}
