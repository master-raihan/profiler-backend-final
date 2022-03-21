<?php

namespace App\Services;

use App\Contracts\Services\FileContract;
use App\Contracts\Repositories\FileRepository;
use App\Helpers\CsvParser;

class FileService implements FileContract
{
    private $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;

    }

    public function uploadCsv($request)
    {
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
                if($request->header == 1)
                {
                    $csvData = $file->read(true);
                    $headings = config('csv.fields');
                }else {
                    $csvData = $file->read(false);
                    $headings = config('csv.fields');
                }
            }
            return ['headings'=> $headings, 'csvData'=>$csvData, 'csvFile' => $uploadedCsvFile];
        }
        return 'File Unavailable';
    }

    public function getFileById($id)
    {
        return $this->fileRepository->getFileById($id);
    }

    public function processCsv($request)
    {

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
                'file_id' => $csvFile->id,
                'user_id' => $csvFile->user_id
            ];
            $tempCsvFileLocation = 'pending-csv-files/temp-'.time().'.json';
            file_put_contents($tempCsvFileLocation, json_encode($response));

            return $tempCsvFileLocation;
        }

        return "File Id Required";
    }

}
