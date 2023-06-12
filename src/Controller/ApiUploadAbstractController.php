<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiUploadAbstractController extends ApiAbstractController
{
    const NO_FILES_PROVIDED_ERROR_MSG = 'No files provided';

    protected function json_error_no_files_provided():JsonResponse
    {
        return $this->json_error(new \Exception(self::NO_FILES_PROVIDED_ERROR_MSG), true);
    }
}
