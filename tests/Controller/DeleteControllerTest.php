<?php

namespace App\Tests\Controller;

use App\Api\Credentials;
use App\Controller\ApiAbstractController;
use App\Controller\ApiDeleteAbstractController;
use App\Controller\ApiUploadAbstractController;
use App\FileManagerService;
use App\Tests\ApiTestCase;


class DeleteControllerTest extends ApiTestCase
{

    public function testErrorAccessDenied(): void
    {

        $json = $this->testDeleteAbstract([], Credentials::APIKEY_READONLY, false);

        $this->assertJsonErrorMessage($json, ApiAbstractController::ACCESS_DENIED_ERROR_MSG);
    }

    public function testErrorNoHashesProvided(): void
    {

        $json = $this->testDeleteAbstract([], Credentials::APIKEY_WRITE, false);

        $this->assertJsonErrorMessage($json, ApiDeleteAbstractController::NO_HASHES_PROVIDED_ERROR_MSG);
    }

    public function testDeleteHash(): void
    {

        $hash = $this->doUploadSingleFile();
        
        $json = $this->testDeleteAbstract([$hash], Credentials::APIKEY_WRITE);

        $this->assertTrue(in_array($hash, $json->deleted_hashes));
    }

    public function testDeleteNonExistentHash(): void
    {

        $hash = 'non_existent_hash';
        
        $json = $this->testDeleteAbstract([$hash], Credentials::APIKEY_WRITE);

        $this->assertFalse(in_array($hash, $json->deleted_hashes));
    }
    
}
