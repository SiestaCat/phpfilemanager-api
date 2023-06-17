<?php

namespace App\Controller;

use App\Api\Credentials;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiAbstractController extends AbstractController
{

    protected ?string $apikey = null;

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
        return $this->json_error(new \Exception(self::ACCESS_DENIED_ERROR_MSG), true)->setStatusCode(403);
    }

    private function setApiKeyFromRequest(Request $request):void
    {
        $this->apikey = $request->query->get('apikey');

        $this->apikey = $this->apikey === null ? $request->request->get('apikey') : $this->apikey;
    }

    protected function checkApiKey(Request $request, int $apikey_type):bool
    {
        $this->setApiKeyFromRequest($request);

        if($this->apikey === null) return false;

        $status = false;

        switch($apikey_type)
        {
            case Credentials::APIKEY_READONLY:
                $status = $this->apikey === $_ENV['APIKEY_READONLY'] || $this->apikey === $_ENV['APIKEY_WRITE'];
                break;
            case Credentials::APIKEY_WRITE:
                $status = $this->apikey === $_ENV['APIKEY_WRITE'];
                break;
        }

        if(!$status && $this->getParameter('kernel.environment') === 'prod')
        {
            if(rand(1,2) === 1)
            {
                usleep(rand(1000,100000));
                sleep(rand(1,2));
            }
        }

        return $status;
    }
}
