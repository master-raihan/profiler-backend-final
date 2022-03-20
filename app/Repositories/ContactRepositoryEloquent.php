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
        return $this->model()->upsert($contact);
    }
}
