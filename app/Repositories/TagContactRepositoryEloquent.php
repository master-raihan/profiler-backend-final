<?php


namespace App\Repositories;

use App\Contracts\Repositories\TagContactRepository;
use App\Models\TagContact;
use App\Repositories\BaseRepository\BaseRepository;

class TagContactRepositoryEloquent extends BaseRepository implements TagContactRepository
{
    protected function model()
    {
        return new TagContact();
    }

    public function setTagContact($data)
    {
        return $this->model->insert($data);
    }
}
