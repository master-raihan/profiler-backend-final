<?php

namespace App\Contracts\Services;

interface ContactContract
{
    public function uploadContact();
    public function getAllContactsByAuthUser();
}
