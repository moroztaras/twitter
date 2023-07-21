<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
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
        // Use faker
        $faker = Factory::create('EN_en');

        $admin = new User();
        $admin->setEmail('admin@twitter.ua')
            ->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setRoles([User::ROLE_ADMIN])
            ->setPlainPassword('qwerty')
            ->setPassword($this->passwordEncoder->hashPassword($admin, 'qwerty'))
            ->setBirthday($faker->dateTime)
            ->setGender('male')
            ->setCountry($faker->country)
            ->setApiKey('abcdaaaa-1234-5678-dddd-000000000001')
            ->setUuid(Uuid::uuid4())
        ;

        $user = new User();
        $user->setEmail($faker->email)
            ->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setRoles([User::ROLE_USER])
            ->setPlainPassword('qwerty')
            ->setPassword($this->passwordEncoder->hashPassword($admin, 'qwerty'))
            ->setBirthday($faker->dateTime)
            ->setGender('male')
            ->setCountry($faker->country)
            ->setApiKey(Uuid::uuid4()->toString())
            ->setUuid(Uuid::uuid4())
        ;

        $users = [
            self::USER_ADMIN => $admin,
            self::USER => $user,
        ];

        foreach ($users as $user) {
            $manager->persist($user);
        }

        $manager->flush();

        // Add References - link from other fixture on this fixture
        foreach ($users as $code => $user) {
            $this->addReference($code, $user);
        }
    }
}
