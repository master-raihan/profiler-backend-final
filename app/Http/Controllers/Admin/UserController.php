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
        return $this->userService->getAllUsers();
    }

    public function createUser(Request $request)
    {
        return response()->json($this->userService->createUser($request));
    }

    public function editUser($id)
    {
        return $this->userService->editUser($id);
    }

    public function updateUser(Request $request)
    {
        $response = $this->userService->updateUser($request);

        return response()->json('User Updated successfully!', $response['status']);


    }

    public function deleteUser($id)
    {

         if($this->userService->deleteUser( (int) $id)){
             return response()->json('Deleted successfully!');
         }
         else{
             return response()->json('Error!');
         }

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
