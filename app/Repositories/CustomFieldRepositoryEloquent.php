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

    public function addCustomField($field)
    {
        $customFieldDataResponse = $this->model->where('contact_id', $field['contact_id'])->where('user_id', $field['user_id'])->where('field_name', $field['field_name'])->first();

        if ($customFieldDataResponse !== null) {
            $customFieldDataResponse->update(['field_value' => $field['field_value']]);
        } else {
            $customFieldDataResponse = $this->model->create($field);
        }
        return $customFieldDataResponse;
    }

    public function getCustomFieldByUser($user_id)
    {
        return $this->model->where("user_id", (int) $user_id)->get();
    }

    public function deleteCustomField($fieldName)
    {
        return $this->model->where("field_name", $fieldName)->where("user_id", Auth::guard('user')->user()->id)->delete();
    }
}
