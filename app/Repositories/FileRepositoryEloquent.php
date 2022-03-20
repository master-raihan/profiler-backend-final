<?php


namespace App\Repositories;

use App\Contracts\Repositories\FileRepository;
use App\Repositories\BaseRepository\BaseRepository;
use App\Models\File;

class FileRepositoryEloquent extends BaseRepository implements FileRepository
{

    protected function model()
    {
        return new File();
    }

    public function uploadCsv($file)
    {
        return $this->model()->create($file);
    }

    public function getFileById($id)
    {
        return $this->model()->find($id);
    }
}
