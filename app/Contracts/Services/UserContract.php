<?php

namespace App\Contracts\Services;


interface UserContract {
    public function getAllUsers();
    public function createUser($request);
    public function getUserById($id);
    public function updateUser($request);
    public function deleteUser($id);
    public function getUserTags($id);
}
