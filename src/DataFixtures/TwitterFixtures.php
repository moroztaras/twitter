<?php

namespace App\DataFixtures;

use App\Entity\Twitter;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TwitterFixtures extends Fixture  implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Get references from categorises
        /** @var User $admin */
        $admin = $this->getReference(UserFixtures::USER_ADMIN);

        $twitter = (new Twitter())
            ->setText('Text')
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
