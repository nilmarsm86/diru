<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Role;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $roles = [Role::ROLE_CLIENT, Role::ROLE_INVESTOR, Role::ROLE_DRAFTSMAN, Role::ROLE_DIRECTOR, Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN];
        $i = 1;
        foreach ($roles as $role){
            $rol = $manager->getRepository(Role::class)->findOneBy(['name' => $role]);
            if(is_null($rol)){
                $manager->persist(new Role($role, $i));
                $i++;
            }
        }

        $manager->flush();
    }
}
