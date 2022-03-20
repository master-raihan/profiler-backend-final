<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use  App\Http\Controllers\Controller;
use App\Contracts\Services\TagContract;

class TagController extends Controller
{
    private $tagService;

    public function __construct(TagContract $tagService)
    {
        $this->tagService = $tagService;

    }
    public function getAllTags()
    {
        $serviceResponse = $this->tagService->getAllTags();
        return response()->json($serviceResponse, $serviceResponse['status']);
    }

    public function createTag(Request $request)
    {
        $serviceResponse = $this->tagService->createTag($request);
        return response()->json($serviceResponse, $serviceResponse['status']);
    }


    public function deleteTag($id)
    {
        $serviceResponse = $this->tagService->deleteTag($id);
        return response()->json($serviceResponse, $serviceResponse['status']);

    }



}
