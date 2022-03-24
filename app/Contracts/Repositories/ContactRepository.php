<?php

namespace App\Contracts\Repositories;

interface ContactRepository
{
    public function uploadContact($contact);
    public function getContacts($where);
}
