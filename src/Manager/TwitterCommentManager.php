<?php

namespace App\Manager;

use App\Entity\Twitter;
use App\Entity\TwitterComment;
use App\Entity\User;
use App\Repository\TwitterCommentRepository;
use App\Validator\Helper\ApiObjectValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;

class TwitterCommentManager
{
    private const PAGE_LIMIT = 5;

    /**
     * TwitterCommentManager constructor.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        private ApiObjectValidator $apiObjectValidator,
        private TwitterCommentRepository $twitterCommentRepository,
    ) {
    }

    public function new($content, User $user, Twitter $twitter):TwitterComment
    {
        /** @var TwitterComment $comment */
        $comment = $this->apiObjectValidator->deserializeAndValidate($content, TwitterComment::class, [
            UnwrappingDenormalizer::UNWRAP_PATH => '[twitter-comment]',
            'new' => true,
        ]);

        $comment->setUser($user)->setTwitter($twitter);

        return $this->save($comment);
    }

    // Save twitter comment in DB
    private function save(TwitterComment $comment): TwitterComment
    {
        $this->doctrine->getManager()->persist($comment);
        $this->doctrine->getManager()->flush();

        return $comment;
    }
}
