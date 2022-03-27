<?php

namespace App\Repositories;

use App\Contracts\Repositories\ContactRepository;
use App\Models\Contact;
use App\Repositories\BaseRepository\BaseRepository;

class ContactRepositoryEloquent extends BaseRepository implements ContactRepository
{
    protected function model()
    {
        return new Contact();
    }

    public function uploadContact($contact)
    {
        return $this->model->insert($contact);
    }

    public function getAllContactsByUser($user_id)
    {
        return $this->model->where("user_id", (int) $user_id)->get();
    }

    public function getContacts($contacts)
    {
        return $contacts->get();
    }

    public function getContactsWhere($where)
    {
        return $this->model->where($where)->get();
    }
}
