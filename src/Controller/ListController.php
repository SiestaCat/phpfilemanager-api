<?php

namespace App\Controller;

use App\Api\Credentials;
use App\FileManagerService;
use Siestacat\Phpfilemanager\File\Repository\AdapterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ListController extends ApiAbstractController
{
    #[Route('/list/{page}/{page_limit}', name: 'app_list', methods: ['GET'], requirements: ['page' => '\d+', 'page_limit' => '\d+'])]
    public function index(FileManagerService $fileManagerService, Request $request, int $page = 1, int $page_limit = AdapterInterface::DEFAULT_PAGE_LIMIT): JsonResponse
    {

        $this->json_data['list'] = [];
        
        if(!$this->checkApiKey($request, Credentials::APIKEY_READONLY)) return $this->json_error_access_denied();
        
        try
        {
            $this->json_data['list'] = $fileManagerService->list($page, $page_limit);

            $this->json_data['next'] = count($this->json_data['list']) > 0 ? $this->getNextPrevUrl($page, $page_limit, true) : null;
            $this->json_data['prev'] = $this->getNextPrevUrl($page, $page_limit, false);
        }
        catch(\Exception $e)
        {
            return $this->json_error($e);
        }

        
        $this->json_data['success'] = true;

        return $this->json($this->json_data);
    }

    private function getNextPrevUrl(int $page, int $page_limit, bool $next):?string
    {
        return (!$next && $page > 1) || $next ? $this->generateUrl($this->container->get('request_stack')->getCurrentRequest()->get('_route'), ['page' => ($next ? $page+1 : $page-1), 'page_limit' => $page_limit, 'apikey' => $this->apikey], UrlGeneratorInterface::ABSOLUTE_URL) : null;
    }
}
