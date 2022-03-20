<?php

namespace App\Repositories;

use App\Contracts\Repositories\TagRepository;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Tag;

class TagRepositoryEloquent extends BaseRepository implements TagRepository
{

    protected function model()
    {
        return new Tag();
    }

    public function getAllTags()
    {
        $allUsers = $this->model->get();
        return $allUsers;
    }

    public function createTag($data){
        return $this->model->create($data);
    }

    public function deleteTag($id){
        return $this->model->where(['id' => $id])->delete();
    }
}
