<?php

namespace App\Manager;

use App\Entity\Twitter;
use App\Entity\User;
use App\Form\Model\TwitterModel;
use App\Repository\TwitterRepository;
use App\Validator\Helper\ApiObjectValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

/**
 * TwitterManager.
 */
class TwitterManager
{
    private const PAGE_LIMIT = 5;

    /**
     * TwitterManager constructor.
     */
    public function __construct(
        private string $photoDir,
        private ManagerRegistry $doctrine,
        private ApiObjectValidator $apiObjectValidator,
        private TwitterRepository $twitterRepository,
        private FileManager $fileManager,
    ) {
    }

    // Web all published twitters of user
    public function list(User $user): array
    {
        return $this->twitterRepository->findAllByUser($user);
    }

    // Web create new twitter
    public function create(User $user, TwitterModel $twitterModel, ?UploadedFile $photo = null): Twitter
    {
        $twitter = (new Twitter())
            ->setText($twitterModel->getText())
            ->setVideo($twitterModel->getVideo())
            ->setUser($user)
        ;

        if ($photo) {
            $twitter->setPhoto($this->fileManager->upload($photo, $this->photoDir));
        }

        $this->save($twitter);

        return $twitter;
    }

    // Web edit twitter from form
    public function editTwitter(Twitter $twitter, TwitterModel $twitterModel, ?UploadedFile $photo = null): Twitter
    {
        if ($photo) {
            $twitter->setPhoto($this->fileManager->upload($photo, $this->photoDir));
        }

        return $this->save(
            $twitter
                ->setText($twitterModel->getText())
                ->setVideo($twitterModel->getVideo())
                ->setUpdatedAt(new \DateTime())
        );
    }

    // Web show twitter
    public function show(Twitter $twitter): Twitter
    {
        $twitter->setViews($twitter->getViews() + 1);
        $this->save($twitter);

        return $twitter;
    }

    // Api list twitters by page
    public function getTwitterPageByUserId(int $id, int $page)
    {
        // Calculate offset
        $offset = max($page - 1, 0) * self::PAGE_LIMIT;

        return $this->twitterRepository->getPageByUserId($id, $offset, self::PAGE_LIMIT);
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
        $twitter->setUpdatedAt(new \DateTime());

        return $this->save($twitter);
    }

    public function getCountTwittersOfUser(User $user): int
    {
        return $this->twitterRepository->countTwittersOfUser($user);
    }

    public function twittersOfFollowing(User $user, int $limit): array
    {
        return $this->twitterRepository->findLastTwittersOfFriends($user, $limit);
    }

    public function createNewTwitterForShare(Twitter $twitter, User $user): Twitter
    {
        return (new Twitter())->setParent($twitter)->setUser($user)->setText(null)->setVideo(null);
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
