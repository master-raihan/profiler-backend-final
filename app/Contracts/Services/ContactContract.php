<?php

namespace App\Contracts\Services;

interface ContactContract
{
    public function uploadContact();
    public function getAllContactsByAuthUser();
    public function addCustomField($request);
    public function filter($request);
    public function getFields();
    public function getCustomFieldByUser();
    public function deleteCustomField($request);
}
