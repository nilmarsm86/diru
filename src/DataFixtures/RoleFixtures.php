<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = [Role::ROLE_CLIENT, Role::ROLE_PLANNER, Role::ROLE_DIRECTOR, Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN];
        foreach ($roles as $role){
            $rol = $manager->getRepository(Role::class)->findOneBy(['name' => $role]);
            if(is_null($rol)){
                $manager->persist(new Role($role));
            }
        }

        $manager->flush();
    }
}
