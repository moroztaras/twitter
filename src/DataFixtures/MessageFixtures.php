<?php

namespace App\DataFixtures;

use App\Entity\Dialogue;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Nonstandard\Uuid;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Use faker
        $faker = Factory::create('EN_en');

        // Get references from users
        /** @var User $admin */
        $admin = $this->getReference(UserFixtures::USER_ADMIN);
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER);

        // Get references from dialogue
        $dialogue = $this->getReference(DialogueFixtures::DIALOGUE);

        $firstMessage = (new Message())
            ->setSender($admin)
            ->setReceiver($user)
            ->setStatus(false)
            ->setMessage('Hi. How are you. What you doing?')
            ->setDialogue($dialogue)
            ->setUuid(Uuid::uuid4());

        $manager->persist($firstMessage);

        $secondMessage = (new Message())
            ->setSender($user)
            ->setReceiver($admin)
            ->setStatus(false)
            ->setMessage('Hi. I\'m fine thanks.')
            ->setDialogue($dialogue)
            ->setUuid(Uuid::uuid4());

        $manager->persist($secondMessage);

        $manager->flush();
    }

    // Depend on User and Dialogue
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            DialogueFixtures::class,
        ];
    }
}
