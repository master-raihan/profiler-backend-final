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

    public function getAllFiles(){
        $serviceResponse = $this->fileService->getAllFiles();
        return response()->json($serviceResponse, $serviceResponse['status']);
    }
    public function uploadCsv(Request $request)
    {
        $serviceResponse = $this->fileService->uploadCsv($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function processCsv(Request $request)
    {
        $serviceResponse = $this->fileService->processCsv($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }
}
