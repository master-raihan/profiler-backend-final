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
        $allUsers = $this->model->get();
        return $allUsers;
    }

    public function getLastUser(){
        return $this->model->latest()->first();
    }

    public function createUser($data){
        return $this->model->create($data);
        // return response()->json('ok');
        // dd('ok');
    }
    public function editUser($id){
        $user = $this->model->find($id);
        return $user;
    }

    public function updateUser($data, $userId) {
        return $this->model->where(['id' => $userId])->update($data);
    }

    public function deleteUser($id){
        return $this->model->where(['id' => $id])->delete();
    }
}
