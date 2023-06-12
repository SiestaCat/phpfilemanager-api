<?php

namespace App\Controller;

use App\Api\Credentials;
use App\FileManagerService;
use Siestacat\Phpfilemanager\Exception\FileNotExistsException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class GetController extends ApiAbstractController
{
    #[Route('/get/{hash}', name: 'app_get', methods: ['GET'])]
    public function index(string $hash, FileManagerService $fileManagerService, Request $request): Response
    {

        if(!$this->checkApiKey($request, Credentials::APIKEY_READONLY)) return new Response('Invalid apikey for this purpose', 403);

        try
        {
            $file = $fileManagerService->getFileCommander()->get($hash);

            return new BinaryFileResponse($file->getPath());

        }
        catch(FileNotExistsException $e)
        {
            throw $this->createNotFoundException(sprintf('File with hash "%s" not found', $hash));
        }
        
        throw $this->createNotFoundException();
    }
}
