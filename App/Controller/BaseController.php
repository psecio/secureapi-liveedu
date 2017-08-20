<?php

namespace App\Controller;

class BaseController
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Magic method to check against the container for a property
     * like it was on the class (no need for getContainer -> get call)
     *
     * @param string $name Property name
     * @return mixed Either null or the matching service
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }

    public function jsonResponse($response, $status, $message, array $data = [])
    {
        $output = [
            'status' => $status,
            'message' => $message
        ];
        if (!empty($data)) {
            $output['data'] = $data;
        }

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
