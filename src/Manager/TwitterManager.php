<?php

namespace App\Manager;

use App\Entity\Twitter;
use App\Entity\User;
use App\Validator\Helper\ApiObjectValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

/**
 * TwitterManager.
 */
class TwitterManager
{
    /**
     * TwitterManager constructor.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        private ApiObjectValidator $apiObjectValidator,
    ) {
    }

    // Create new twitter
    public function new(string $content, User $user): Twitter
    {
        /** @var Twitter $twitter */
        $twitter = $this->apiObjectValidator->deserializeAndValidate($content, Twitter::class, [
            UnwrappingDenormalizer::UNWRAP_PATH => '[twitter]',
           'new' => true,
        ]);
        $twitter->setUser($user);

        return $this->save($twitter);
    }

    // Save twitter in DB
    private function save(Twitter $twitter): Twitter
    {
        $this->doctrine->getManager()->persist($twitter);
        $this->doctrine->getManager()->flush();

        return $twitter;
    }
}
