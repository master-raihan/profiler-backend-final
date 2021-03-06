<?php

namespace App\Helpers;

class UtilityHelper
{
    public static function RETURN_SUCCESS_FORMAT($statusCode, $message, $data = [])
    {

        return [
            'status' => $statusCode,
            'success' => true,
            'message' => $message ,
            'data' => $data
        ];
    }

    public static function RETURN_ERROR_FORMAT($status_code, $message = "Something is wrong !!")
    {
        return [
            'success' => false,
            'message' => $message,
            'status' => $status_code,
            'data' => null
        ];
    }

    public static function findIndexOfKey($key_to_index,$array){
        return array_search($key_to_index,array_keys($array));
    }
}
