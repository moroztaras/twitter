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
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Use faker
        $faker = Factory::create('EN_en');

        $admin = new User();
        $admin->setEmail($faker->email)
            ->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setRoles([User::ROLE_ADMIN])
            ->setPlainPassword('qwerty')
            ->setPassword($this->passwordEncoder->hashPassword($admin, 'qwerty'))
            ->setBirthday($faker->dateTime)
            ->setGender('male')
            ->setCountry($faker->country)
            ->setApiKey(Uuid::uuid4()->toString())
            ->setUuid(Uuid::uuid4())
        ;

        $manager->persist($admin);

        $manager->flush();
    }
}
