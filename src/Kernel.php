<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        $dir = $_SERVER['DIRU_DATA_DIR'] ?? null;

        return \is_string($dir) && '' !== $dir
            ? $dir.'/cache/'.$this->environment
            : parent::getCacheDir();
    }

    public function getLogDir(): string
    {
        $dir = $_SERVER['DIRU_DATA_DIR'] ?? null;

        return \is_string($dir) && '' !== $dir
            ? $dir.'/log'
            : parent::getLogDir();
    }
}
