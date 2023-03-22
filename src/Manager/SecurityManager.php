<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityManager
{
    /**
     * SecurityManager constructor.'.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        private UserPasswordHasherInterface $passwordEncoder,
    ) {
    }

    // Create new user
    public function create(User $user): User
    {
        $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPlainPassword()));
        $this->save($user);

        return $user;
    }

    // Save user in DB
    private function save(User $user): void
    {
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
    }
}
