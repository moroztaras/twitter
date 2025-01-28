<?php

namespace App\DataFixtures;

use App\Entity\Dialogue;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Nonstandard\Uuid;

class DialogueFixtures extends Fixture implements DependentFixtureInterface
{
    final public const DIALOGUE = 'dialogue';

    public function load(ObjectManager $manager): void
    {
        // Get references from users
        /** @var User $admin */
        $admin = $this->getReference(UserFixtures::USER_ADMIN);
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER);

        $dialogue = (new Dialogue())
            ->setCreator($admin)
            ->setReceiver($user)
            ->setUuid(Uuid::uuid4());

        $dialogues = [
            self::DIALOGUE => $dialogue,
        ];

        $manager->persist($dialogue);

        $manager->flush();

        // Add References - link from other fixture on this fixture
        foreach ($dialogues as $code => $dialogue) {
            $this->addReference($code, $dialogue);
        }
    }

    // Depend on User
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
