<?php

namespace App\Services;

use App\Contracts\Repositories\TagRepository;
use App\Contracts\Services\UserContract;
use App\Contracts\Repositories\UserRepository;
use App\Contracts\Repositories\ContactRepository;
use App\Contracts\Repositories\CustomFieldRepository;
use App\Helpers\UtilityHelper;
use App\Models\Contact;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Support\Facades\Validator;
use App\Variables\Variable;

class UserService implements UserContract
{
    private $userRepository;
    private $tagRepository;
    private $variable;
    private $customFieldRepository;
    private $contactRepository;

    public function __construct(UserRepository $userRepository, TagRepository $tagRepository, Variable $variable, CustomFieldRepository $customFieldRepository, ContactRepository $contactRepository)
    {
        $this->userRepository = $userRepository;
        $this->tagRepository = $tagRepository;
        $this->variable = $variable;
        $this->customFieldRepository = $customFieldRepository;
        $this->contactRepository = $contactRepository;
    }

    public function getAllUsers()
    {
        try{
            return UtilityHelper::RETURN_SUCCESS_FORMAT(
                ResponseAlias::HTTP_OK,
                'All Users Successfully Fetched!',
                $this->userRepository->getAllUsers()
            );
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function getLastUser(){
        return $this->userRepository->getLastUser();
    }

    public function createUser($request){
        try{
            //validation
            $rules = [
                'username' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $user = [
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => $request->status
            ];
            $userData = $this->userRepository->createUser($user);

            $tag = [
                'user_id' => $userData['id'],
                'tag_value' => 'default',
                'is_default' => $this->variable->DEFAULT_VALUE
            ];
            $this->tagRepository->createTag($tag);
            return UtilityHelper::RETURN_SUCCESS_FORMAT(
                ResponseAlias::HTTP_OK,
                'A User Successfully Created!',
                $userData
            );

        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public  function editUser($id){
        try{
            return UtilityHelper::RETURN_SUCCESS_FORMAT(
                ResponseAlias::HTTP_OK,
                'A User Successfully Created!',
                $this->userRepository->editUser($id)
            );
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function updateUser($request){
        try {
            //validation
            $rules = [
                'username' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique:users,email, ' . (int) $request['id'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $user = [
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email
            ];

            if ($this->userRepository->updateUser($user, (int) $request['id'])) {

                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'User Updated!', []);
            }

            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, 'Failed To Update User!');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function deleteUser($id){
        try{
            if($this->userRepository->deleteUser($id)){
                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'User Successfully Deleted',[]);
            }

            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, 'Failed To Delete User',[]);
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
        }
    }

    public function addCustomField($request){
        try{
            $customFieldData = [
                'user_id' => 1,
                'custom_fields' => json_encode($request->custom_fields)
            ];
            $customFieldData = $this->customFieldRepository->addCustomField($customFieldData);
            if($customFieldData){
                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Custom Field Successfully Added',$customFieldData);
            }
        }catch(\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function filter($request){
        try{
            if($request->match == "any"){
                $options = $request->data;
                $i = 0;
                $contacts = Contact::query();
                if($request->query_type == "equal"){
                    foreach($options as $key => $value){
                        if($i==0){
                            $contacts = $contacts->where($key,$value);
                        }else{
                            $contacts = $contacts->orWhere($key,$value);
                        }
                        $i++;
                    }
                    $contactsData = $this->userRepository->getContacts($contacts);
                    if($contactsData){
                        return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Searched Result',$contactsData);
                    }
                }
                if($request->query_type == "start_with"){
                    foreach($options as $key => $value){
                        if($i==0){
                            $contacts->where($key, 'like',$value . '%');
                        }else{
                            $contacts->orWhere($key, 'like',$value . '%');
                        }
                        $i++;
                    }
                    $contactsData = $this->userRepository->getContacts($contacts);
                    if($contactsData){
                        return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Searched Result',$contactsData);
                    }
                }
                if($request->query_type == "end_with"){
                    foreach($options as $key => $value){
                        if($i==0){
                            $contacts->where($key, 'like', '%' . $value);
                        }else{
                            $contacts->orWhere($key, 'like','%' . $value);
                        }
                        $i++;
                    }
                    $contactsData = $this->userRepository->getContacts($contacts);
                    if($contactsData){
                        return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Searched Result',$contactsData);
                    }
                }
            }else{
                if($request->query_type == "equal"){
                    $options = $request->data;
                    $where = [];
                    $contacts = Contact::query();
                    foreach($options as $key => $value){
                        $where[] = [$key,$value];
                    }
                    $contactsData = $this->contactRepository->getContacts($where);
                    if($contactsData){
                        return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Searched Result',$contactsData);
                    }
                }
                if($request->query_type == "start_with"){
                    $options = $request->data;
                    $where = [];
                    foreach($options as $key => $value){
                        $where[] = [$key, 'like', $value . '%'];
                    }
                    $contactsData = $this->contactRepository->getContacts($where);
                    if($contactsData){
                        return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Searched Result',$contactsData);
                    }
                }
                if($request->query_type == "end_with"){
                    $options = $request->data;
                    $where = [];
                    foreach($options as $key => $value){
                        $where[] = [$key, 'like', '%' . $value];
                    }
                    $contactsData = $this->contactRepository->getContacts($where);
                    if($contactsData){
                        return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Searched Result',$contactsData);
                    }
                }
            }
        }catch(\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }
}
