<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = [], $message = "Success", $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public static function error($message = "Something went wrong", $code = 400, $errors = [])
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}
