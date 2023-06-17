<?php

namespace App\Controller;

use App\Api\Credentials;
use App\FileManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadController extends ApiUploadAbstractController
{
    #[Route('/upload', name: 'app_upload', methods: ['POST'])]
    public function index(FileManagerService $fileManagerService, Request $request): JsonResponse
    {

        $this->json_data['files'] = [];

        if(!$this->checkApiKey($request, Credentials::APIKEY_WRITE)) return $this->json_error_access_denied();

        /**
         * @var UploadedFile[]
         */
        $uploaded_files = $request->files;

        

        if(count($uploaded_files) === 0) return $this->json_error_no_files_provided();

        foreach($uploaded_files as $upload_file)
        {
            if(is_array($upload_file)) $upload_file = $upload_file[0];
            try
            {
                $file = $fileManagerService->getFileCommander()->add($upload_file->getPathname());

                $this->json_data['files'][] = (object) [
                    'filename' => $upload_file->getClientOriginalName(),
                    'hash' => $file->getHash()
                ];
            }
            catch(\Exception $e)
            {
                return $this->json_error($e);
            }
            
        }

        $this->json_data['success'] = true;

        return $this->json($this->json_data);
    }
}
