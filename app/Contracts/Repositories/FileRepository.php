<?php

namespace App\Contracts\Repositories;

interface FileRepository
{
    public function getAllFiles();
    public function uploadCsv($file);
    public function getFileById($id);
}
