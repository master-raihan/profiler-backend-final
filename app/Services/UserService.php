<?php

namespace App\Services;

use App\Contracts\Repositories\TagRepository;
use App\Contracts\Services\UserContract;
use App\Contracts\Repositories\UserRepository;
use App\Helpers\UtilityHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Validator;
use App\Variables\Variable;

class UserService implements UserContract
{
    private $userRepository;
    private $tagRepository;
    private $variable;

    public function __construct(UserRepository $userRepository, TagRepository $tagRepository, Variable $variable)
    {
        $this->userRepository = $userRepository;
        $this->tagRepository = $tagRepository;
        $this->variable = $variable;

    }

    public function getAllUsers()
    {
        try {
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "User Fetched Successfully", $this->userRepository->getAllUsers());
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Something went wrong!!!");
        }
    }

    public function getLastUser(){
        try{
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "Last User Fetched", $this->userRepository->getLastUser());
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Something is wrong !!");
        }
    }

    public function createUser($request){

        try{
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

            $tagData = $this->tagRepository->createTag($tag);

            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "New User Created", ["user"=> $userData, "user_tag" => $tagData]);
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Something went wrong!!");
        }
    }

    public  function editUser($id){
        try{
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "Edit User", $this->userRepository->editUser($id));
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Some thing went wrong!!");
        }
    }

    public function updateUser($request){
        try {

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
}
