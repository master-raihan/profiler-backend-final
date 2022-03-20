<?php
namespace App\Http\Controllers\Admin;

use App\Contracts\Services\ContactContract;
use App\Contracts\Services\FileContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    private $fileService;
    private $contactService;

    public function __construct(FileContract $fileService, ContactContract $contactContract)
    {
        $this->fileService = $fileService;
        $this->contactService = $contactContract;
    }
    public function uploadCsv(Request $request)
    {
        return response()->json($this->fileService->uploadCsv($request));
    }

    public function processCsv(Request $request)
    {
        return response()->json($this->fileService->processCsv($request));
    }

    public function test()
    {
        return response()->json($this->contactService->uploadContact());
    }

}
