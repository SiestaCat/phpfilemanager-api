<?php

namespace App\Tests;

use App\Api\Credentials;
use App\FileManagerService;
use Siestacat\Phpfilemanager\File\FileCommander;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelBrowser;

class ApiTestCase extends WebTestCase
{

    private ?HttpKernelBrowser $browser_cient = null;

    const SUCCESS_STATUS_PROP = 'success';

    protected function getApiKeyParameters(int $apikey_type, array $parameters = [], ?string $apikey = null)
    {


        if($apikey !== null)
        {
            $parameters['apikey'] = $apikey;

            return $parameters;
        }

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

    protected function getUploadFilePath(?string $filename = null):string
    {
        return __DIR__ . '/upload_files/' . $filename;
    }

    protected function getJson(KernelBrowser $client, bool $assert_success_status = false, array $properties_exists_assert = []):?\stdClass
    {
        $json = json_decode($client->getResponse()->getContent(), false);

        $this->assertIsObject($json);

        $json = is_object($json) ? $json : null;

        if($assert_success_status)
        {
            $this->assertApiSuccess($json);

            foreach($properties_exists_assert as $property_name)
            {
                $this->assertTrue(property_exists($json, $property_name), sprintf('Property "%s" not exists in json', $property_name));
            }
        }

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

    protected function _bootKernel():void
    {
        if(self::$booted) return;
        static::bootKernel();
    }

    protected function _createClient():HttpKernelBrowser
    {
        $this->browser_cient = $this->browser_cient === null ? static::createClient() : $this->browser_cient;
        return $this->browser_cient;
    }

    protected function getFileCommander():FileCommander
    {
        self::_bootKernel();

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

    protected function doUploadSingleFile(): string
    {

        $json = $this->testUploadMultipleFilesAbstract([$this->getSampleFilesList()[0]]);

        $this->assertJsonUploadedFiles($json);

        return $json->files[0]->hash;
    }

    protected function doUploadMultipleFiles(): array
    {

        $json = $this->testUploadMultipleFilesAbstract($this->getSampleFilesList());

        $this->assertJsonUploadedFiles($json);

        return $json->files;
    }

    private function getSampleFilesList():array
    {
        $files = [];

        foreach(scandir($this->getUploadFilePath()) as $filename)
        {
            if(in_array($filename, ['.', '..'])) continue;

            $files[] = $filename;
        }

        return $files;
    }

    protected function assertJsonUploadedFiles(\stdClass $json): void
    {

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

    protected function testUploadMultipleFilesAbstract(array $filesnames = [], int $apikey_type = Credentials::APIKEY_WRITE, bool $assert_success_status = true):?\stdClass
    {
        $files = [];

        foreach($filesnames as $filename)
        {
            $files[] = $this->uploadFile($filename);
        }

        $client = $this->_createClient();
        $client->request('PUT', '/upload', $this->getApiKeyParameters($apikey_type), $files);
        
        return $this->getJson($client, $assert_success_status, ['files']);
    }

    protected function testDeleteAbstract(array $hashes = [], int $apikey_type = Credentials::APIKEY_WRITE, bool $assert_success_status = true):?\stdClass
    {
        $client = $this->_createClient();
        $client->request('DELETE', '/delete', $this->getApiKeyParameters($apikey_type, ['hashes' => $hashes]));
        
        return $this->getJson($client, $assert_success_status, ['deleted_hashes']);
    }

    protected function testExistsAbstract(string $hash, int $apikey_type = Credentials::APIKEY_READONLY, bool $assert_success_status = true, ?string $apikey = null):?\stdClass
    {
        $client = $this->_createClient();
        $client->request('GET', '/exists/' . $hash, $this->getApiKeyParameters($apikey_type, [], $apikey));
        
        return $this->getJson($client, $assert_success_status, ['exists']);
    }

    protected function testGetAbstract(string $hash, int $apikey_type = Credentials::APIKEY_READONLY, ?string $apikey = null):Response
    {
        $client = $this->_createClient();
        $client->request('GET', '/get/' . $hash, $this->getApiKeyParameters($apikey_type, [], $apikey));

        $client->getInternalResponse()->getContent();

        return $client->getResponse();
        
    }
}
