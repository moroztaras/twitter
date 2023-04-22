<?php

namespace App\Manager;

use App\Entity\Twitter;
use App\Entity\User;
use App\Form\Twitter\Model\TwitterModel;
use App\Repository\TwitterRepository;
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
        private TwitterRepository $twitterRepository
    ) {
    }

    // Web all published twitters of user
    public function list(User $user): array
    {
        return $this->twitterRepository->findBy(['user' => $user]);
    }

    // Web create new twitter
    public function create(User $user, TwitterModel $twitterModel): Twitter
    {
        $twitter = (new Twitter())
            ->setText($twitterModel->getText())
            ->setVideo($twitterModel->getVideo())
            ->setUser($user)
        ;

        $this->save($twitter);

        return $twitter;
    }

    // Show twitter
    public function show(Twitter $twitter): Twitter
    {
        $twitter->setViews($twitter->getViews() + 1);
        $this->save($twitter);

        return $twitter;
    }

    // Api create new twitter
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
