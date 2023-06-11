<?php

namespace App\Tests\Controller;

use App\Api\Credentials;
use App\FileManagerService;
use App\Tests\ApiTestCase;


class UploadControllerTest extends ApiTestCase
{
    public function testUploadSingleFile(): void
    {

        $json = $this->testUploadMultipleFiles(['image.jpg']);

        if(!$json) return;

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

    private function testUploadMultipleFiles(array $filesnames = [], int $apikey_type = Credentials::APIKEY_WRITE):?\stdClass
    {
        $files = [];

        foreach($filesnames as $filename)
        {
            $files[] = $this->uploadFile($filename);
        }

        $client = static::createClient();
        $client->request('PUT', '/upload', $this->getApiKeyParameters($apikey_type), $files);
        
        return $this->getJson($client, true);
    }
    
}
