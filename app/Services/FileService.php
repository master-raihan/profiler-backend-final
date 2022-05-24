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
            if($request->hasFile('csvFile'))
            {
                $allowedFileExtension=['csv'];
                $csvFile = $request->file('csvFile');
                $extension = $csvFile->getClientOriginalExtension();
                $check = in_array($extension, $allowedFileExtension);

                if($check){

                    $fileOriginalName = time() . '-' .$csvFile->getClientOriginalName();
                    $data = file($csvFile);
                    $chunks = array_chunk($data, 1000);

                    $filePublicLocation = 'csv-files/'.time() . '-' .explode('.', $csvFile->getClientOriginalName())[0];

                    mkdir($filePublicLocation);

                    foreach ($chunks as $key => $chunk) {
                        $path = file_put_contents($filePublicLocation.'/'.$key.'.csv', $chunk);
                    }

                    $file = [
                        'user_id' => $request->user_id,
                        'status' => $request->status,
                        'file_location' => $filePublicLocation
                    ];

                    $uploadedCsvFile = $this->fileRepository->uploadCsv($file);
                    $file = new CsvParser();
                    $csvFile = $this->fileRepository->getFileById($uploadedCsvFile->id);
                    $file->load($csvFile->file_location.'/0.csv');
                    $headings = config('csv.fields');

                    if($request->header == 1)
                    {
                        $csvData = $file->read();
                    }else {
                        $csvData = $file->read(false);
                    }
                    return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "File Uploaded!", ['headings'=> $headings, 'csvData'=>$csvData, 'csvFile' => $uploadedCsvFile]);
                }
                return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, 'Only Csv File Allowed');
            }
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, 'File Unavailable');
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
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
                    if ($request->json()->all()['fields'][$field] != -1)
                    {
                        $sample[$field] = $request->json()->all()['fields'][$field];
                    }elseif ($request->json()->all()['fields'][$field] == -1) {
                        $sample[$field] = -1;
                    }
                }

                $response = [
                    'mapping'=> $sample,
                    'tags'=> $request->json()->all()['tags'],
                    'file_id' => $csvFile->id,
                    'user_id' => $csvFile->user_id
                ];
                $tempCsvFileLocation = 'pending-csv-files/temp-'.time().'.json';
                file_put_contents($tempCsvFileLocation, json_encode($response));

                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "File Queued For Processing", $tempCsvFileLocation);
            }

            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "File Id Required");
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

}
