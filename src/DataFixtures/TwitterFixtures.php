<?php

namespace App\DataFixtures;

use App\Entity\Twitter;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TwitterFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Use faker
        $faker = Factory::create('EN_en');

        // Get references from categorises
        /** @var User $admin */
        $admin = $this->getReference(UserFixtures::USER_ADMIN);

        $twitter = (new Twitter())
            ->setText($faker->text)
            ->setUser($admin)
        ;

        $manager->persist($twitter);
        $manager->flush();
    }

    // Depend on User
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
