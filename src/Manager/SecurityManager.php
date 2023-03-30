<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityManager
{
    /**
     * SecurityManager constructor.
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
            ->setApiKey(Uuid::uuid4()->toString())
        ;

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
    }
}
