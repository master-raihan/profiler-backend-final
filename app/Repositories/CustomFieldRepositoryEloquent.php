<?php


namespace App\Repositories;

use App\Contracts\Repositories\CustomFieldRepository;
use App\Models\CustomField;
use App\Repositories\BaseRepository\BaseRepository;

class CustomFieldRepositoryEloquent extends BaseRepository implements CustomFieldRepository
{
    protected function model()
    {
        return new CustomField();
    }

    public function addCustomField($customFieldData){
        return $this->model->create($customFieldData);
    }

    public function getCustomFieldByUser($user_id)
    {
        return $this->model->where("user_id", (int) $user_id)->get();
    }
}
