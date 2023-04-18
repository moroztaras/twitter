<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    final public const USER_ADMIN = 'admin';
    final public const USER = 'user';

    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            self::USER_ADMIN => ($admin = new User())
                ->setEmail('email@social.network.loc')
                ->setFirstName('firstName')
                ->setLastName('lastName')
                ->setRoles([User::ROLE_ADMIN])
                ->setPlainPassword('qwerty')
                ->setPassword($this->passwordEncoder->hashPassword($admin, 'qwerty'))
                ->setBirthday(new \DateTimeImmutable('1990-01-01'))
                ->setGender('male')
                ->setCountry('Ukraine')
                ->setApiKey(Uuid::uuid4()->toString())
                ->setUuid(Uuid::uuid4()),
        ];

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();

        foreach ($users as $code => $user) {
            $this->addReference($code, $user);
        }
    }
}
