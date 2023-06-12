<?php

namespace App\Controller;

use App\Api\Credentials;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiAbstractController extends AbstractController
{

    const ACCESS_DENIED_ERROR_MSG = 'Access denied';

    protected $json_data = ['success' => false];

    protected function json_error(?\Exception $e = null, bool $show_error_always = false):JsonResponse
    {
        if($e !== null && ($show_error_always || $this->getParameter('kernel.environment') !== 'prod'))
        {
            $this->json_data['error'] = $e->getMessage();
        }

        $this->json_data['success'] = false;

        return $this->json($this->json_data);
    }

    protected function json_error_access_denied():JsonResponse
    {
        return $this->json_error(new \Exception(self::ACCESS_DENIED_ERROR_MSG), true);
    }

    protected function checkApiKey(Request $request, int $apikey_type):bool
    {

        $apikey = $request->request->get('apikey');

        switch($apikey_type)
        {
            case Credentials::APIKEY_READONLY:
                return $apikey === $_ENV['APIKEY_READONLY'] || $apikey === $_ENV['APIKEY_WRITE'];
            case Credentials::APIKEY_WRITE:
                return $apikey === $_ENV['APIKEY_WRITE'];
        }

        return false;
    }
}
