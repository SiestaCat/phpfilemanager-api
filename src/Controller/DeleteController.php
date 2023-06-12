<?php

namespace App\Controller;

use App\Api\Credentials;
use App\FileManagerService;
use Siestacat\Phpfilemanager\Exception\FileNotExistsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DeleteController extends ApiDeleteAbstractController
{
    #[Route('/delete', name: 'app_delete', methods: ['DELETE'])]
    public function index(FileManagerService $fileManagerService, Request $request): JsonResponse
    {

        $this->json_data['deleted_hashes'] = [];

        if(!$this->checkApiKey($request, Credentials::APIKEY_WRITE)) return $this->json_error_access_denied();

        /**
         * @var string[]
         */
        $hashes = $request->get('hashes');

        if(!(is_array($hashes) && count($hashes) > 0)) return $this->json_error_no_hashes_provided();

        foreach($hashes as $hash)
        {
            try
            {
                if($fileManagerService->getFileCommander()->del($hash))
                {
                    $this->json_data['deleted_hashes'][] = $hash;
                }
            }
            catch(FileNotExistsException $e)
            {}
            catch(\Exception $e)
            {
                return $this->json_error($e);
            }
            
        }

        $this->json_data['success'] = true;

        return $this->json($this->json_data);
    }
}
