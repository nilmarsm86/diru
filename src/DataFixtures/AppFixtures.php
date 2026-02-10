<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/uploads')] private string $uploadsDirectory,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $filesystem = new Filesystem();
        if ($filesystem->exists($this->uploadsDirectory)) {
            $filesystem->remove($this->uploadsDirectory);
        }

        try {
            $filesystem->mkdir($this->uploadsDirectory);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
