<?php

namespace App\Api;


class Credentials
{
    public function __construct(private string $apikey_readonly, private string $apikey_write)
    {}

    public function getApiKeyReadOnly():string
    {
        return $this->apikey_readonly;
    }

    public function getApiKeyWrite():string
    {
        return $this->apikey_write;
    }
}