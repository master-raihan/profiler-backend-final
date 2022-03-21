<?php

namespace App\Services;

use App\Contracts\Repositories\ContactRepository;
use App\Contracts\Repositories\FileRepository;
use App\Contracts\Services\ContactContract;
use App\Helpers\CsvParser;
use App\Helpers\UtilityHelper;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ContactService implements ContactContract
{
    private $contactRepository;
    private $fileRepository;

    public function __construct(ContactRepository $contactRepository, FileRepository $fileRepository)
    {
        $this->contactRepository = $contactRepository;
        $this->fileRepository = $fileRepository;
    }

    public function uploadContact()
    {
        try{
            $files = glob("pending-csv-files/*.json");

            $string = file_get_contents($files[0]);
            $json_a = json_decode($string,true);
            $pendingFile = $this->fileRepository->getFileById($json_a['file_id']);

            $csvData = new CsvParser();
            $csvData->load('csv-files/'.$pendingFile['file_name_location']);
            $response = array();
            foreach ($csvData->read() as $row) {
                $sample = array();
                foreach (config('csv.fields_sample') as $index => $field) {
                    $sample[$field] = $row[$json_a['mapping'][$field]];
                }
                $response[] = $sample;
            }
            return UtilityHelper::RETURN_SUCCESS_FORMAT(
                ResponseAlias::HTTP_OK,
                'All Contacts Successfully Uploaded!',
                $response
            );

        }catch(\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }
}
