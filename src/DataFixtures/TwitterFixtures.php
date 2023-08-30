<?php

namespace App\DataFixtures;

use App\Entity\Twitter;
use App\Entity\TwitterComment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Nonstandard\Uuid;

class TwitterFixtures extends Fixture implements DependentFixtureInterface
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

        for ($i = 1; $i <= 5; ++$i) {
            $twitter = (new Twitter())
                ->setText($faker->word)
                ->setPhoto('Motivation.jpeg')
                ->setVideo('https://www.youtube.com/embed/19tIt3D_yiI')
                ->setUser($admin)
                ->setUuid(Uuid::uuid4())
            ;

            $manager->persist($twitter);

            for ($j = 1; $j <= 5; ++$j) {
                $twitterComment = (new TwitterComment())
                    ->setComment($faker->word)
                    ->setUser($admin)
                    ->setTwitter($twitter)
                ;
                $manager->persist($twitterComment);
            }
        }

        for ($i = 1; $i <= 5; ++$i) {
            $twitter = (new Twitter())
                ->setText($faker->word)
                ->setPhoto('Motivation.jpeg')
                ->setVideo('https://www.youtube.com/embed/19tIt3D_yiI')
                ->setUser($user)
                ->setUuid(Uuid::uuid4())
            ;

            $manager->persist($twitter);
        }

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
