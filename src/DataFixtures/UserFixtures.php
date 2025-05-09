<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Role;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->createInactive($manager);
        $this->createClient($manager);
        $this->createInvestor($manager);
        $this->createDraftsman($manager);
        $this->createDirector($manager);
        $this->createAdmin($manager);
        $this->createSuperAdmin($manager);
        $manager->flush();
    }

    /**
     * @throws Exception
     */
    private function register(UserPasswordHasherInterface $userPasswordHasher, User $user, Role $baseRol): void
    {
        $user->changePassword($userPasswordHasher);
        $user->activate();
        $user->addRole($baseRol);
    }

    /**
     * @param ObjectManager $manager
     * @param User $user
     * @param array $roles
     * @return void
     * @throws Exception
     */
    private function save(ObjectManager $manager, User $user, array $roles): void
    {
        $this->register($this->userPasswordHasher, $user, $roles[0]);
        foreach($roles as $role){
            if($role->getId() !== $roles[0]->getId()){
                $user->addRole($role);
            }
        }

        $manager->persist($user);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    private function createSuperAdmin(ObjectManager $manager): void
    {
        $adminUser = $manager->getRepository(User::class)->findOneBy(['username' => 'superadmin']);
        if(is_null($adminUser)){
            $roles = $manager->getRepository(Role::class)->findAll();

            $admin = new User('SuperAdmin', 'User', 'superadmin', 'superadmin', 7);
            $this->save($manager, $admin, $roles);
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    private function createAdmin(ObjectManager $manager): void
    {
        $adminUser = $manager->getRepository(User::class)->findOneBy(['username' => 'admin']);
        if(is_null($adminUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== Role::ROLE_SUPER_ADMIN;
            });

            $admin = new User('Admin', 'User', 'admin', 'admin', 6);
            $this->save($manager, $admin, $roles);
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    private function createDirector(ObjectManager $manager): void
    {
        $directorUser = $manager->getRepository(User::class)->findOneBy(['username' => 'director']);
        if(is_null($directorUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== Role::ROLE_SUPER_ADMIN && $role->getName() !== Role::ROLE_ADMIN;
            });

            $boss = new User('Director', 'User', 'director', 'director', 5);
            $this->save($manager, $boss, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    private function createDraftsman(ObjectManager $manager): void
    {
        $draftsmanUser = $manager->getRepository(User::class)->findOneBy(['username' => 'draftsman']);
        if(is_null($draftsmanUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== Role::ROLE_SUPER_ADMIN &&
                       $role->getName() !== Role::ROLE_ADMIN &&
                       $role->getName() !== Role::ROLE_DIRECTOR;
            });

            $planner = new User('Draftsman', 'User', 'draftsman', 'draftsman', 4);
            $this->save($manager, $planner, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    private function createInvestor(ObjectManager $manager): void
    {
        $investorUser = $manager->getRepository(User::class)->findOneBy(['username' => 'investor']);
        if(is_null($investorUser)){
            $roles = $manager->getRepository(Role::class)->findAll();
            $roles = array_filter($roles, function ($role){
                return $role->getName() !== Role::ROLE_SUPER_ADMIN &&
                       $role->getName() !== Role::ROLE_ADMIN &&
                       $role->getName() !== Role::ROLE_DIRECTOR &&
                       $role->getName() !== Role::ROLE_DRAFTSMAN;
            });

            $planner = new User('Investor', 'User', 'investor', 'investor', 3);
            $this->save($manager, $planner, array_values($roles));
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    private function createClient(ObjectManager $manager): void
    {
        $clientUser = $manager->getRepository(User::class)->findOneBy(['username' => 'client']);
        if(is_null($clientUser)){
            $role = $manager->getRepository(Role::class)->findOneBy(['name'=>Role::ROLE_CLIENT]);

            $user = new User('Client', 'User', 'client', 'client', 2);
            $this->save($manager, $user, [$role]);
        }
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    private function createInactive(ObjectManager $manager): void
    {
        $inactiveUser = $manager->getRepository(User::class)->findOneBy(['username' => 'inactive']);
        if(is_null($inactiveUser)){
            $role = $manager->getRepository(Role::class)->findOneBy(['name'=>Role::ROLE_CLIENT]);

            $user = new User('Inactive', 'User', 'inactive', 'inactive', 1);
            $this->register($this->userPasswordHasher, $user, $role);
            $user->addRole($role);
            $user->deactivate();

            $manager->persist($user);
        }
    }
}
