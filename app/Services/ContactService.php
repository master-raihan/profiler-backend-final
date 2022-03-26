<?php

namespace App\Services;

use App\Contracts\Repositories\ContactRepository;
use App\Contracts\Repositories\FileRepository;
use App\Contracts\Services\ContactContract;
use App\Helpers\CsvParser;
use App\Helpers\UtilityHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            $files = glob("public/pending-csv-files/*.json");
            $response = array();
            foreach ($files as $file){
                $string = file_get_contents($file);
                $json_a = json_decode($string,true);
                $pendingFile = $this->fileRepository->getFileById($json_a['file_id']);
                if($pendingFile->status == 1){
                    $pendingFile->save();
                    $csvData = new CsvParser();
                    $csvData->load('public/csv-files/'.$pendingFile['file_name_location']);

                    foreach ($csvData->read() as $row) {
                        $sample = array();
                        $sample['user_id'] = $json_a['user_id'];
                        foreach (config('csv.fields') as $index => $field) {
                            if($json_a['mapping'][$field] != -1){
                                $sample[$field] = $this->resolveNull($row[$json_a['mapping'][$field]]);
                            }
                        }
                        $response[] = $sample;
                    }
                    if($this->contactRepository->uploadContact($response)){
                        $pendingFile->status = 3;
                        $pendingFile->save();
                    }else {
                        $pendingFile->status = 0;
                        $pendingFile->save();
                    }
                }
            }
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK,
                "File Contact Uploaded Successfully",
                $response
            );
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function getAllContactsByAuthUser()
    {
        try{
            $contactsByUser = $this->contactRepository->getAllContactsByUser(Auth::guard('user')->user()->id);
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "Contacts by User Fetched", $contactsByUser);
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Something is wrong !!");
        }
    }

    public function resolveNull($text)
    {
        if(empty($text)) {
            return null;
        }
        else {
            return $text;
        }
    }
}
