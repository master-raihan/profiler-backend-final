<?php

namespace App\Contracts\Repositories;

interface ContactRepository
{
    public function uploadContact($contact);
    public function getAllContactsByUser($user_id);
    public function getContacts($contacts);
    public function getContactsWhere($where);
}
