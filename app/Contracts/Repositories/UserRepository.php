<?php

namespace App\Contracts\Repositories;

interface UserRepository
{
    public function getAllUsers();
    public function createUser($user);
    public function editUser($id);
    public function updateUser($user, $id);
    public function deleteUser($id);
    public function getLastUser();
}
