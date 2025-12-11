<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $roles = [Role::ROLE_CLIENT, Role::ROLE_INVESTOR, Role::ROLE_DRAFTSMAN, Role::ROLE_DIRECTOR, Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN];
        $i = 1;
        foreach ($roles as $role) {
            $rol = $manager->getRepository(Role::class)->findOneBy(['name' => $role]);
            if (is_null($rol)) {
                $manager->persist(new Role($role, $i));
                ++$i;
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default'];
    }
}
