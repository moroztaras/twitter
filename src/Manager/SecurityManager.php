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

    // Save user in DB
    public function save(User $user, string $password): void
    {
        $user
            ->setPassword($this->passwordEncoder->hashPassword($user, $password))
            ->setTokenRecover(null)
        ;
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
    }
}
