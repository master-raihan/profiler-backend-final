<?php

namespace App\Services;

use App\Contracts\Repositories\TagRepository;
use App\Contracts\Services\UserContract;
use App\Contracts\Repositories\UserRepository;
use App\Helpers\UtilityHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
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

    public function createUser($request){

        try{
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return UtilityHelper::RETURN_ERROR_FORMAT(
                    ResponseAlias::HTTP_BAD_REQUEST,
                    $validator->errors()
                );
            }
            $user = [
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
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Something went wrong!!");
        }
    }

    public  function getUserById($id){
        try{
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "Edit User", $this->userRepository->getUserById($id));
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Some thing went wrong!!");
        }
    }

    public function updateUser($request){
        try {
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => "required|email|unique:users,email,{$request->id}"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return UtilityHelper::RETURN_ERROR_FORMAT(
                    ResponseAlias::HTTP_BAD_REQUEST,
                    $validator->errors()
                );
            }

            $user = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'status' => $request->status
            ];

            if ($this->userRepository->updateUser($user, (int) $request->id)) {
                $updatedUser = $this->getUserById((int) $request->id);
                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'User Updated Successfully!', $updatedUser);
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

    public function getUserTags($id){
        try {
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "User Fetched Successfully", $this->tagRepository->getUserTags($id));
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Something went wrong!!!");
        }
    }
}
