<?php

namespace App\Controller;

class IndexController extends \App\Controller\BaseController
{
    public function index($request, $response)
    {
        $data = [
            'message' => 'test'
        ];
        $response = $response->withJSON($data);
        return $response;
    }

    // This endpoint needs to be protected.
    public function test($request, $response)
    {
        $response = $response->withJSON([
            'res' => 'hooray'
        ]);
        return $response;
    }
}
