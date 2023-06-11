<?php

namespace App;

use Symfony\Component\HttpKernel\KernelInterface;

class FileManagerService
{

    const DEFAULT_DATA_DIR_ABS_PATH = 'data';

    private string $abs_data_path;

    public function __construct(?string $data_dir, KernelInterface $kernel)
    {

        $data_dir
        =
        (
            $data_dir === null || $data_dir === ''
        )
        ?
            $kernel->getProjectDir() . '/' . self::DEFAULT_DATA_DIR_ABS_PATH
        :
            $data_dir
        ;

        $realpath = realpath($data_dir);

        if($realpath === false && !mkdir($data_dir, 0777, true))
        {
            throw new \Exception('Unable to create data dir "' . dirname($data_dir) . '"');
        }

        $this->abs_data_path = $realpath;
    }
}