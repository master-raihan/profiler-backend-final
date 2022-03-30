<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\User;


class UserRepositoryEloquent extends BaseRepository implements UserRepository
{

    protected function model()
    {
        return new User();
    }

    public function getAllUsers()
    {
        return  $this->model->get();
    }

    public function getLastUser(){
        return $this->model->latest()->first();
    }

    public function createUser($user)
    {
        return $this->model->create($user);
    }
    public function getUserById($id)
    {
        return $this->model->find($id);
    }

    public function updateUser($user, $id)
    {
        return $this->model->find($id)->update($user);
    }

    public function deleteUser($id)
    {
        return $this->model->find($id)->delete();
    }
}
