<?php

namespace App\Tests\Controller;

use App\Api\Credentials;
use App\Controller\ApiAbstractController;
use App\Controller\ApiDeleteAbstractController;
use App\Controller\ApiUploadAbstractController;
use App\FileManagerService;
use App\Tests\ApiTestCase;


class ExistsControllerTest extends ApiTestCase
{

    public function testErrorAccessDenied(): void
    {
        $json = $this->testExistsAbstract('hash', Credentials::APIKEY_READONLY, false, 'fake_apikey');

        $this->assertJsonErrorMessage($json, ApiAbstractController::ACCESS_DENIED_ERROR_MSG);
    }

    public function testExistent(): void
    {

        $hash = $this->doUploadSingleFile();

        $json = $this->testExistsAbstract($hash, Credentials::APIKEY_READONLY, false);

        $this->assertTrue($json->exists);

        $this->testDeleteAbstract([$hash]);
        
    }

    public function testNonExistent(): void
    {

        $json = $this->testExistsAbstract('this_hash_not_exists', Credentials::APIKEY_READONLY, false);

        $this->assertFalse($json->exists);
        
    }

    
}
