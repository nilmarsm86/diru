<?php

namespace App\DataFixtures;

use App\Entity\Enums\State;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createClient($manager);
        $this->createPlanner($manager);
        $this->createDirector($manager);
        $this->createAdmin($manager);
        $this->createSuperAdmin($manager);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @param User $user
     * @param array $roles
     * @return void
     */
    private function save(ObjectManager $manager, User $user, array $roles): void
    {
        $user->register($this->userPasswordHasher, $roles[0]);
        foreach($roles as $role){
            if($role->getId() !== $roles[0]->getId()){
                $user->addRole($role);
            }
        }
        $user->setState(State::Active);

        $manager->persist($user);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createSuperAdmin(ObjectManager $manager): void
    {
        $adminUser = $manager->getRepository(User::class)->findOneBy(['username' => 'superadmin']);
        if(is_null($adminUser)){
            $roles = $manager->getRepository(Role::class)->findAll();

            $admin = new User('SuperAdmin', 'User', 'superadmin', 'superadmin');
            $this->save($manager, $admin, $roles);
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createAdmin(ObjectManager $manager): void
    {
        $adminUser = $manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        if(is_null($adminUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== Role::ROLE_SUPER_ADMIN;
            });

            $admin = new User('Admin', 'User', 'admin', 'admin');
            $this->save($manager, $admin, $roles);
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createDirector(ObjectManager $manager): void
    {
        $directorUser = $manager->getRepository(User::class)->findOneBy(['username' => 'director']);
        if(is_null($directorUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== Role::ROLE_SUPER_ADMIN && $role->getName() !== Role::ROLE_ADMIN;
            });

            $boss = new User('Director', 'User', 'director', 'director');
            $this->save($manager, $boss, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createPlanner(ObjectManager $manager): void
    {
        $plannerUser = $manager->getRepository(User::class)->findOneBy(['username' => 'planner']);
        if(is_null($plannerUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== Role::ROLE_SUPER_ADMIN && $role->getName() !== Role::ROLE_ADMIN && $role->getName() !== Role::ROLE_DIRECTOR;
            });

            $planner = new User('Planner', 'User', 'planner', 'planner');
            $this->save($manager, $planner, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function createClient(ObjectManager $manager): void
    {
        $clientUser = $manager->getRepository(User::class)->findOneBy(['username' => 'client']);
        if(is_null($clientUser)){
            $role = $manager->getRepository(Role::class)->findOneBy(['name'=>Role::ROLE_CLIENT]);

            $user = new User('Client', 'User', 'client', 'client');
            $this->save($manager, $user, [$role]);
        }
    }

}
