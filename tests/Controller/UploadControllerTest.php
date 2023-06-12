<?php

namespace App\Tests\Controller;

use App\Api\Credentials;
use App\Controller\ApiAbstractController;
use App\FileManagerService;
use App\Tests\ApiTestCase;


class UploadControllerTest extends ApiTestCase
{
    public function testUploadSingleFile(): void
    {

        $json = $this->testUploadMultipleFiles(['image.jpg']);

        $fileCommander = $this->getFileCommander();

        foreach($json->files as $file)
        {
            $hash = $fileCommander->hash_file
            (
                $this->getUploadFilePath($file->filename)
            );

            $this->assertEquals
            (
                $hash,
                $file->hash,
                sprintf('Filename "%s" hash', $file->filename)
            );
        }
    }

    public function testErrorAccessDenied(): void
    {

        $json = $this->testUploadMultipleFiles([], Credentials::APIKEY_READONLY, false);

        $this->assertJsonErrorMessage($json, ApiAbstractController::ACCESS_DENIED_ERROR_MSG);
    }

    public function testErrorNoFilesProvided(): void
    {

        $json = $this->testUploadMultipleFiles([], Credentials::APIKEY_WRITE, false);

        $this->assertJsonErrorMessage($json, ApiAbstractController::NO_FILES_PROVIDED_ERROR_MSG);
    }

    private function testUploadMultipleFiles(array $filesnames = [], int $apikey_type = Credentials::APIKEY_WRITE, bool $assert_success_status = true):?\stdClass
    {
        $files = [];

        foreach($filesnames as $filename)
        {
            $files[] = $this->uploadFile($filename);
        }

        $client = static::createClient();
        $client->request('PUT', '/upload', $this->getApiKeyParameters($apikey_type), $files);
        
        return $this->getJson($client, $assert_success_status);
    }
    
}
