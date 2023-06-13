<?php

namespace App\Tests\Controller;

use App\Api\Credentials;
use App\Controller\ApiAbstractController;
use App\Controller\ApiDeleteAbstractController;
use App\Controller\ApiUploadAbstractController;
use App\FileManagerService;
use App\Tests\ApiTestCase;


class GetControllerTest extends ApiTestCase
{

    public function testErrorAccessDenied(): void
    {
        $response = $this->testGetAbstract('hash', Credentials::APIKEY_READONLY, false, 'fake_apikey');

        $this->assertEquals(403, $response->getStatusCode(), 'Response code should be 403');

        $this->assertStringContainsString($response->getContent(), 'Invalid apikey');
    }

    public function testExistent(): void
    {

        $clear_hashes = [];

        foreach($this->doUploadMultipleFiles() as $file)
        {

            $clear_hashes[] = $file->hash;

            $response = $this->testGetAbstract($file->hash, Credentials::APIKEY_READONLY);

            $this->assertEquals(200, $response->getStatusCode(), 'Response code should be 200');

            //How to simulate download?
            //Maybe memory leaks for large files

            
        }

        //CLEAR:
        $this->testDeleteAbstract($clear_hashes);
    }

    public function testNotExistent(): void
    {

        $response = $this->testGetAbstract('this_hash_not_exists', Credentials::APIKEY_READONLY);

        $this->assertEquals(404, $response->getStatusCode(), 'Response code should be 404');
    }
}
