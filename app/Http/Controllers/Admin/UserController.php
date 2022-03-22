<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contact;
use Illuminate\Http\Request;
use  App\Http\Controllers\Controller;
use App\Contracts\Services\UserContract;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserContract $userService)
    {
        $this->middleware('auth:api');
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

    public function filter(Request $request){
        $options = $request->toArray();
        $i = 0;
        $where = [];
        foreach($options as $key => $value){
            $keys[] = $key;
            foreach($value as $v){
                $val[] = $v;
                $where[] = [$keys[$i],$val[$i]];
            }
            $i++;
        }
        $contacts = Contact::where($where)->get();


//        $contacts = Contact::query();
//        for($i=0; $i<count($keys); $i++){
//            $contacts = $contacts->where($keys[$i],$val[$i]);
//        }
//        $contacts = $contacts->get();

        return $contacts;
    }
}
//$order_details[] = [
//    'order_id' => $orders->id,
//    'product_id' => $product['product_id'][$i],
//    'quantity' => $product['quantity'][$i],
//    'unit_price' => $product['price'][$i],
//    'amount' => $product['amount'][$i],
//];
