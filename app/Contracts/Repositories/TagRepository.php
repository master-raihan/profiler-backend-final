<?php

namespace App\Contracts\Repositories;

interface TagRepository
{
    public function getAllTags();
    public function createTag($tag);
    public function deleteTag($id);
}
