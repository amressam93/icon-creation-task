<?php

namespace App\Http\Traits;

trait ResponseTrait
{
    protected function apiResponse($message,$data = null,$code = 200)
    {
        $data = ($data != null)? $data: (object)[];
        $response['response']['message'] = $message;
        $response['response']['data'] = $data;
        return response()->json($response, $code);
    }

    protected function successResponse ($message,$data = null) {
        return $this->apiResponse($message,$data,200);
    }

    protected function validationResponse ($validation) {
        $message = $validation->errors()->first();
        return $this->apiResponse($message,(object)[],422);
    }

    protected function logicErrorResponse ($message,$data = null) {
        return $this->apiResponse($message,$data,444);
    }

    protected function unauthorizedResponse ($message,$data = null) {
        return $this->apiResponse($message,$data,401);
    }

    protected function unauthorizedPermissionResponse ($message,$data = null) {
        return $this->apiResponse($message,$data,403);
    }

    protected function forbiddenResponse ($message = null,$data = null) {
        return $this->apiResponse($message,$data,403);
    }

    protected function notFoundResponse ($message = null,$data = null) {
        return $this->apiResponse($message,$data,404);
    }

    protected function customResponse ($message,$data = null,$code = 200) {
        return $this->apiResponse($message,$data,$code);
    }
}
