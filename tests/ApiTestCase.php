<?php

namespace App\Tests;

use App\Api\Credentials;
use App\FileManagerService;
use Siestacat\Phpfilemanager\File\FileCommander;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ApiTestCase extends WebTestCase
{

    const SUCCESS_STATUS_PROP = 'success';

    protected function getApiKeyParameters(int $apikey_type, array $parameters = [])
    {

        switch($apikey_type)
        {
            case Credentials::APIKEY_READONLY:
                $parameters['apikey'] = $_ENV['APIKEY_READONLY'];
                break;
            case Credentials::APIKEY_WRITE:
                $parameters['apikey'] = $_ENV['APIKEY_WRITE'];
                break;
        }

        return $parameters;
    }

    protected function uploadFile(string $filename):UploadedFile
    {
        return new UploadedFile($this->getUploadFilePath($filename), $filename);
    }

    protected function getUploadFilePath(string $filename):string
    {
        return __DIR__ . '/upload_files/' . $filename;
    }

    protected function getJson(KernelBrowser $client, bool $assert_success_status = false):?\stdClass
    {
        $json = json_decode($client->getResponse()->getContent(), false);

        $this->assertIsObject($json);

        $json = is_object($json) ? $json : null;

        if($assert_success_status) $this->assertApiSuccess($json);

        return $json;
    }

    protected function assertApiSuccess(?\stdClass $json)
    {

        $success_status = false;

        if(is_object($json))
        {
            $success_property_exists = property_exists($json, self::SUCCESS_STATUS_PROP);

            $this->assertTrue($success_property_exists, sprintf('Api "%s" property exists', self::SUCCESS_STATUS_PROP));

            if($success_property_exists) $success_status = $json->{self::SUCCESS_STATUS_PROP};
        }

        $this->assertTrue($success_status, 'Api success status');
    }

    protected function getFileCommander():FileCommander
    {
        static::bootKernel();

        /**
         * @var FileManagerService
         */
        $file_manager_service = self::$kernel->getContainer()->get('file.manager.service');

        return $file_manager_service->getFileCommander();
    }

    protected function assertJsonErrorMessage(\stdClass $json, string $message):void
    {
        $property_error_exists = property_exists($json, 'error');

        $this->assertTrue($property_error_exists);

        if($property_error_exists)
        {
            $this->assertEquals($json->error, $message);
        }
    }
}
