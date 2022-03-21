<?php
namespace App\Http\Controllers\Admin;

use App\Contracts\Services\FileContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    private $fileService;

    public function __construct(FileContract $fileService)
    {
        $this->fileService = $fileService;
    }
    public function uploadCsv(Request $request)
    {
        return response()->json($this->fileService->uploadCsv($request));
    }

    public function processCsv(Request $request)
    {
        return response()->json($this->fileService->processCsv($request));
    }
}
