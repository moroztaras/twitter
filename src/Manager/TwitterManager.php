<?php

namespace App\Manager;

use App\Entity\Twitter;
use App\Entity\User;
use App\Validator\Helper\ApiObjectValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
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

    // Edit twitter
    public function edit(string $content, Twitter $twitter): Twitter
    {
        $validationGroups = ['edit'];
        $this->apiObjectValidator->deserializeAndValidate($content, Twitter::class, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $twitter,
            AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
            UnwrappingDenormalizer::UNWRAP_PATH => '[twitter]',
        ], $validationGroups);

        return $this->save($twitter);
    }

    // Remove twitter from DB
    public function remove(Twitter $twitter): void
    {
        $this->doctrine->getManager()->remove($twitter);
        $this->doctrine->getManager()->flush();
    }

    // Save twitter in DB
    private function save(Twitter $twitter): Twitter
    {
        $this->doctrine->getManager()->persist($twitter);
        $this->doctrine->getManager()->flush();

        return $twitter;
    }
}
