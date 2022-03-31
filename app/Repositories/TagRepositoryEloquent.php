<?php

namespace App\Repositories;

use App\Contracts\Repositories\TagRepository;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;

class TagRepositoryEloquent extends BaseRepository implements TagRepository
{

    protected function model()
    {
        return new Tag();
    }

    public function getAllTags()
    {
        return $this->model->get();
    }

    public function createTag($tag)
    {
        Log::info($tag);
        return $this->model->create($tag);
    }

    public function deleteTag($id)
    {
        return $this->model->find($id)->delete();
    }

    public function getUserTags($id)
    {
        return $this->model->where(['user_id' => $id])->get();
    }
}
