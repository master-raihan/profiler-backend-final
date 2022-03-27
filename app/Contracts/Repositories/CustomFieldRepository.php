<?php

namespace App\Contracts\Repositories;

interface CustomFieldRepository
{
    public function addCustomField($customFieldData);
    public function getCustomFieldByUser($user_id);
}
