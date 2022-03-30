<?php

namespace App\Services;

use App\Contracts\Services\FileContract;
use App\Contracts\Repositories\FileRepository;
use App\Helpers\CsvParser;
use App\Helpers\UtilityHelper;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FileService implements FileContract
{
    private $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function getAllFiles()
    {
        try{
            $allFiles = $this->fileRepository->getAllFiles();
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "All File Fetched!", $allFiles);
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Failed To Fetch Files", $exception->getMessage());
        }
    }

    public function uploadCsv($request)
    {
        try{
            if($request->has('csvFile'))
            {
                $allowedFileExtension=['csv'];
                $csvFile = $request->file('csvFile');
                $extension = $csvFile->getClientOriginalExtension();
                $check = in_array($extension, $allowedFileExtension);

                if($check){
                    $fileLocation = time() . '-' .$csvFile->getClientOriginalName();
                    $csvFile->move('csv-files', $fileLocation);

                    $file = [
                        'user_id' => $request->user_id,
                        'status' => $request->status,
                        'file_location' => $fileLocation
                    ];

                    $uploadedCsvFile = $this->fileRepository->uploadCsv($file);

                    $file = new CsvParser();
                    $csvFile = $this->fileRepository->getFileById($uploadedCsvFile->id);
                    $file->load('csv-files/'.$csvFile->file_location);
                    $headings = config('csv.fields');
                    if($request->header == 1)
                    {
                        $csvData = $file->read();
                    }else {
                        $csvData = $file->read(false);
                    }
                    $this->processTags($request,$uploadedCsvFile);
                }
                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "File Uploaded!", ['headings'=> $headings, 'csvData'=>$csvData, 'csvFile' => $uploadedCsvFile]);
            }
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, 'File Unavailable');
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function processTags($request,$uploadedCsvFile){
        $response = [
            'file_id'=> $uploadedCsvFile->id,
            'user_id' => $request->user_id,
            'tags' => $request->tags
        ];
        $tempTagFileLocation = 'pending-tags-files/temp-'.time().'.json';
        file_put_contents($tempTagFileLocation, json_encode($response));
    }


    public function getFileById($id)
    {
        try{
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "Single File Fetched", $this->fileRepository->getFileById($id));
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }


    public function processCsv($request)
    {
        try{
            if($request->has('csvFileId'))
            {
                $csvFile = $this->fileRepository->getFileById($request->csvFileId);
                $sample = array();
                $headings = config('csv.fields');
                foreach (config('csv.fields') as $field) {
                    Log::info($request->json()->all());
                    if ($request->json()->all()['fields'][$field] != -1)
                    {
                        $sample[$field] = $request->json()->all()['fields'][$field];
                    }elseif ($request->json()->all()['fields'][$field] == -1) {
                        $sample[$field] = -1;
                    }
                }

                $response = [
                    'mapping'=> $sample,
                    'file_id' => $csvFile->id,
                    'user_id' => $csvFile->user_id
                ];
                $tempCsvFileLocation = 'pending-csv-files/temp-'.time().'.json';
//                Log::info($tempCsvFileLocation);
                file_put_contents($tempCsvFileLocation, json_encode($response));

                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "File Queued For Processing", $tempCsvFileLocation);
            }

            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "File Id Required");
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

}
