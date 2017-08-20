<?php

namespace App\Traits;

trait JsonResponse
{
    public function jsonResponse($response, $status, $message, array $data = [])
    {
        $output = [
            'success' => false,
            'message' => $message
        ];
        $response = $response->withJSON($output);
        return $response;
    }
    public function jsonSuccess($response, $message, array $data = [])
    {
        return $this->jsonResponse($response, true, $message, $data);
    }
    public function jsonError($response, $message, array $data = [])
    {
        return $this->jsonResponse($response, false, $message, $data);
    }
}
