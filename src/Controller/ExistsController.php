<?php

namespace App\Controller;

use App\Api\Credentials;
use App\FileManagerService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExistsController extends ApiAbstractController
{
    #[Route('/exists/{hash}', name: 'app_exists', methods: ['GET'])]
    public function index(string $hash, FileManagerService $fileManagerService, Request $request): Response
    {

        $this->json_data['exists'] = false;

        if(!$this->checkApiKey($request, Credentials::APIKEY_READONLY)) return $this->json_error_access_denied();

        try
        {
            $this->json_data['exists'] = $fileManagerService->getFileCommander()->exists($hash);
            $this->json_data['success'] = true;
        }
        catch(\Exception $e)
        {
            return $this->json_error($e);
        }
        
        return $this->json($this->json_data);
    }
}
