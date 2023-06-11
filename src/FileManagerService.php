<?php

namespace App;

use Symfony\Component\HttpKernel\KernelInterface;
use Siestacat\Phpfilemanager\File\Repository\Adapter\FileSystemAdapter;
use Siestacat\Phpfilemanager\File\FileCommander;

class FileManagerService
{

    const DEFAULT_DATA_DIR_ABS_PATH = 'data';

    private FileCommander $fileCommander;

    public function __construct(?string $data_dir, KernelInterface $kernel)
    {

        //Define data dir

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

        $abs_data_path = $data_dir;

        //Init file commander

        $this->fileCommander = new FileCommander
        (
            new FileSystemAdapter($abs_data_path)
        );
    }

    public function getFileCommander():FileCommander
    {
        return $this->fileCommander;
    }
}