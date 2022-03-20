<?php

namespace App\Contracts\Services;


interface FileContract
{
    public function uploadCsv($request);

    public function getFileById($id);

    public function processCsv($request);
}
