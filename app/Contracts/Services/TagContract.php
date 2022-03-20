<?php

namespace App\Contracts\Services;


interface TagContract {
    public function getAllTags();
    public function createTag($request);
    public function deleteTag($id);
}
