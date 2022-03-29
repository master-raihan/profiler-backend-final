<?php

namespace App\Services;

use App\Contracts\Repositories\ContactRepository;
use App\Contracts\Repositories\CustomFieldRepository;
use App\Contracts\Repositories\FileRepository;
use App\Contracts\Services\ContactContract;
use App\Helpers\CsvParser;
use App\Helpers\UtilityHelper;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ContactService implements ContactContract
{
    private $contactRepository;
    private $fileRepository;
    private $customFieldRepository;

    public function __construct(ContactRepository $contactRepository, FileRepository $fileRepository, CustomFieldRepository $customFieldRepository)
    {
        $this->contactRepository = $contactRepository;
        $this->fileRepository = $fileRepository;
        $this->customFieldRepository = $customFieldRepository;
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
                    $csvData->load('public/csv-files/'.$pendingFile['file_location']);

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
                        if(file_exists($file)){
                            unlink($file);
                        }
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

    public function addCustomField($request){
        try{
            $customFieldData = [
                'user_id' => Auth::guard('user')->user()->id,
                'contact_id' => $request->contact_id,
                'field_name' => $request->field_name,
                'field_value' => $request->field_value
            ];
            $customFieldData = $this->customFieldRepository->addCustomField($customFieldData);
            if($customFieldData){
                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Custom Field Successfully Added',$customFieldData);
            }
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function deleteCustomField($request){
        try{
            if($this->customFieldRepository->deleteCustomField($request->fieldName)){
                return UtilityHelper::RETURN_SUCCESS_FORMAT(
                    ResponseAlias::HTTP_OK,
                    'A Custom Field Successfully Deleted!',
                    $request->fieldName
                );
            }
        }catch (\Exception $exception){
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function getCustomFieldByUser()
    {
        try{
            $customFieldByUser = $this->customFieldRepository->getCustomFieldByUser(Auth::guard('user')->user()->id);
            return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, "Contacts by User Fetched", $customFieldByUser);
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(ResponseAlias::HTTP_BAD_REQUEST, "Something is wrong !!");
        }
    }

    public function filter($request){
        try{
            if($request->match == "any"){
                $options = $request->queries;
                $contacts = Contact::query();
                foreach($options as $option){
                    switch ($option['condition']){
                        case 'equal':
                            $contacts = $contacts->orWhere($option['fieldName'], '=', $option['fieldValue']);
                            break;
                        case 'start_with':
                            $contacts = $contacts->orWhere($option['fieldName'], 'like',$option['fieldValue'] . '%');
                            break;
                        case 'end_with':
                            $contacts = $contacts->orWhere($option['fieldName'], 'like','%' . $option['fieldValue']);
                            break;
                    }
                }
                $contactsData = $this->contactRepository->getContacts($contacts);
            }elseif ($request->match == "all"){
                    $options = $request->queries;
                    $contacts = Contact::query();
                    foreach($options as $option){
                        switch ($option['condition']){
                            case 'equal':
                                $contacts = $contacts->where($option['fieldName'], '=', $option['fieldValue']);
                                break;
                            case 'start_with':
                                $contacts = $contacts->where($option['fieldName'], 'like',$option['fieldValue'] . '%');
                                break;
                            case 'end_with':
                                $contacts = $contacts->where($option['fieldName'], 'like','%' . $option['fieldValue']);
                                break;
                        }
                    }
                    $contactsData = $this->contactRepository->getContacts($contacts);
            }
            if($contactsData){
                return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Searched Result',$contactsData);
            }
        }catch(\Exception $exception){
            Log::error($exception->getMessage());
            return UtilityHelper::RETURN_ERROR_FORMAT(
                ResponseAlias::HTTP_BAD_REQUEST
            );
        }
    }

    public function getFields()
    {
        return UtilityHelper::RETURN_SUCCESS_FORMAT(ResponseAlias::HTTP_OK, 'Fields fetched', config('csv.fields'));
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
