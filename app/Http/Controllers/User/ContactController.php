<?php

namespace App\Http\Controllers\User;

use App\Contracts\Services\ContactContract;
use  App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function filter(Request $request){
        $serviceResponse = $this->contactService->filter($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function addCustomField(Request $request){
        $serviceResponse = $this->contactService->addCustomField($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function getFields(){
        $serviceResponse = $this->contactService->getFields();
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function getCustomFieldByUser()
    {
        $serviceResponse = $this->contactService->getCustomFieldByUser();
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function deleteCustomField(Request $request)
    {
        $serviceResponse = $this->contactService->deleteCustomField($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

}
