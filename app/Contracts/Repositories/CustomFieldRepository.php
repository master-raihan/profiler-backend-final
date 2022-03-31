<?php

namespace App\Contracts\Repositories;

interface CustomFieldRepository
{
    public function addCustomField($field);
    public function deleteCustomField($fieldName);
    public function getCustomFieldByUser($user_id);
}
