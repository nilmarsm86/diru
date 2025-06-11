<?php

namespace App\DataFixtures;

use App\Entity\Constructor;
use App\Entity\Contract;
use App\Entity\NetworkConnection;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NetworkConnectionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $connections = ['Hidráulica', 'Electricidad', 'Sanitaria', 'Corrientes débiles'];
        foreach ($connections as $connection) {
            $connectionEntity = $manager->getRepository(NetworkConnection::class)->findOneBy(['name' => $connection]);
            if (is_null($connectionEntity)) {
                $connectionEntity = new NetworkConnection();
                $connectionEntity->setName($connection);

                $manager->persist($connectionEntity);
            }
        }

        $manager->flush();
    }
}
