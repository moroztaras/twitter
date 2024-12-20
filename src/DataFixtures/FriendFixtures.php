<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Friend;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FriendFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Get references from users
        /** @var User $admin */
        $admin = $this->getReference(UserFixtures::USER_ADMIN);
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER);

        $friend = (new Friend())
            ->setUser($user)
            ->setFriend($admin)
            ->setStatus(0);

        $manager->persist($friend);

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
