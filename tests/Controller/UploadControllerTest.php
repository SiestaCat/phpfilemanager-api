<?php

namespace App\Tests\Controller;

use App\Api\Credentials;
use App\Controller\ApiAbstractController;
use App\Controller\ApiUploadAbstractController;
use App\FileManagerService;
use App\Tests\ApiTestCase;


class UploadControllerTest extends ApiTestCase
{
    public function testUploadSingleFile(): void
    {
        $this->doUploadSingleFile();
    }

    public function testErrorAccessDenied(): void
    {

        $json = $this->testUploadMultipleFilesAbstract([], Credentials::APIKEY_READONLY, false);

        $this->assertJsonErrorMessage($json, ApiAbstractController::ACCESS_DENIED_ERROR_MSG);
    }

    public function testErrorNoFilesProvided(): void
    {

        $json = $this->testUploadMultipleFilesAbstract([], Credentials::APIKEY_WRITE, false);

        $this->assertJsonErrorMessage($json, ApiUploadAbstractController::NO_FILES_PROVIDED_ERROR_MSG);
    }

    
    
}
