<?php

namespace App\Services;

use App\Contracts\Services\FileContract;
use App\Contracts\Repositories\FileRepository;
use App\Helpers\CsvParser;
use App\Helpers\UtilityHelper;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FileService implements FileContract
{
    private $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;

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
                    $name = time() . '-' .$csvFile->getClientOriginalName();
                    $csvFile->move('csv-files', $name);

                    $file = [
                        'user_id' => 1,
                        'file_name_location' => $name
                    ];

                    $uploadedCsvFile = $this->fileRepository->uploadCsv($file);

                    $file = new CsvParser();
                    $csvFile = $this->fileRepository->getFileById($uploadedCsvFile->id);
                    $file->load('csv-files/'.$csvFile->file_name_location);
                    if($request->has('header'))
                    {
                        $csvData = $file->read(true);
                        $headings = $file->headings();
                    }else {
                        $csvData = $file->read(false);
                    }
                }
                return UtilityHelper::RETURN_SUCCESS_FORMAT(
                    ResponseAlias::HTTP_OK,
                    'Uploaded Csv file!',
                    ['headings'=>$headings ?? null, 'csvData'=>$csvData, 'csvFile' => $uploadedCsvFile]
                );
            }
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, 'Failed To Update Csv!');
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function getFileById($id)
    {
        try{
            return UtilityHelper::RETURN_SUCCESS_FORMAT(
                ResponseAlias::HTTP_OK,
                'Get the file!',
                $this->fileRepository->getFileById($id)
            );
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function processCsv($request)
    {
        try{
            if($request->has('csvFileId'))
            {
                $csvFile = $this->fileRepository->getFileById($request->csvFileId);

//            if($request->has('header'))
//            {
//                $csvData = $file->read(true);
//                $headings = $file->headings();
//            }else {
//                $csvData = $file->read(false);
//            }

                $sample = array();
                $headings = config('csv.fields_sample');
                foreach (config('csv.fields_sample') as $index => $field) {
                    if ($request->has('header')) {
                        if ($request->fields[$field] != -1)
                        {
                            $sample[$field] = $headings[$request->fields[$field]];
                        }
                    } else {
                        if ($request->fields[$index] != -1) {
                            $sample[$field] = $headings[$request->fields[$index]];
                        }
                    }
                }

                $response = [
                    'mapping'=> $sample,
                    'file_id' => $csvFile->id,
                    'user_id' => $csvFile->user_id
                ];


//            foreach ($csvData as $row) {
//                $sample = array();
//
//                foreach (config('csv.fields_sample') as $index => $field) {
//                    if ($request->has('header')) {
//                        if ($request->fields[$field] != -1)
//                        {
//                            $sample[$field] = $row[$request->fields[$field]];
//                        }
//                    } else {
//                        if ($request->fields[$index] != -1) {
//                            $sample[$field] = $row[$request->fields[$index]];
//                        }
//                    }
//                }
//                $response[] = $sample;
//            }
            }
            $tempCsvFileLocation = 'pending-csv-files/temp-'.time().'.json';
            file_put_contents($tempCsvFileLocation, json_encode($response));

            return UtilityHelper::RETURN_SUCCESS_FORMAT(
                ResponseAlias::HTTP_OK,
                'Processed All Csv Data!',
                $tempCsvFileLocation
            );
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

}
