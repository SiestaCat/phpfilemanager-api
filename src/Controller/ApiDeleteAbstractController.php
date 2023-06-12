<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiDeleteAbstractController extends ApiAbstractController
{

    const NO_HASHES_PROVIDED_ERROR_MSG = 'No hashes provided';

    protected function json_error_no_hashes_provided():JsonResponse
    {
        return $this->json_error(new \Exception(self::NO_HASHES_PROVIDED_ERROR_MSG), true);
    }
}
