<?php


namespace App\Repositories;

use App\Contracts\Repositories\CustomFieldRepository;
use App\Models\CustomField;
use App\Repositories\BaseRepository\BaseRepository;
use Illuminate\Support\Facades\Auth;

class CustomFieldRepositoryEloquent extends BaseRepository implements CustomFieldRepository
{
    protected function model()
    {
        return new CustomField();
    }

    public function addCustomField($customFieldData){
        $customFieldDataResponse = $this->model->where('contact_id', $customFieldData['contact_id'])->where('user_id', $customFieldData['user_id'])->where('field_name', $customFieldData['field_name'])->first();

        if ($customFieldDataResponse !== null) {
            $customFieldDataResponse->update(['field_value' => $customFieldData['field_value']]);
        } else {
            $customFieldDataResponse = $this->model->create($customFieldData);
        }
        return $customFieldDataResponse;
    }

    public function getCustomFieldByUser($user_id)
    {
        return $this->model->where("user_id", (int) $user_id)->get();
    }

    public function deleteCustomField($id)
    {
        return $this->model->where("field_name", $id)->where("user_id", Auth::guard('user')->user()->id)->delete();
    }
}
