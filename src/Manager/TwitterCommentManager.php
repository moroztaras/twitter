<?php

namespace App\Manager;

use App\Entity\Twitter;
use App\Entity\TwitterComment;
use App\Entity\User;
use App\Form\Model\TwitterCommentModel;
use App\Validator\Helper\ApiObjectValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
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
    ) {
    }

    public function createNewComment(Twitter $twitter, User $user, TwitterCommentModel $commentModel): TwitterComment
    {
        return $this->save((new TwitterComment())
            ->setTwitter($twitter)
            ->setUser($user)
            ->setComment($commentModel->getComment()))
        ;
    }

    public function editComment(TwitterComment $comment, TwitterCommentModel $twitterCommentModel): TwitterComment
    {
        $comment->setComment($twitterCommentModel->getComment());

        return $this->save($comment);
    }

    public function new($content, User $user, Twitter $twitter): TwitterComment
    {
        /** @var TwitterComment $comment */
        $comment = $this->apiObjectValidator->deserializeAndValidate($content, TwitterComment::class, [
            UnwrappingDenormalizer::UNWRAP_PATH => '[twitter-comment]',
            'new' => true,
        ]);

        $comment->setUser($user)->setTwitter($twitter);

        return $this->save($comment);
    }

    public function edit($content, TwitterComment $comment): TwitterComment
    {
        $validationGroups = ['edit'];
        $this->apiObjectValidator->deserializeAndValidate($content, TwitterComment::class, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $comment,
            AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
            UnwrappingDenormalizer::UNWRAP_PATH => '[twitter-comment]',
        ], $validationGroups);

        $comment->setUpdatedAt(new \DateTime());

        return $this->save($comment);
    }

    public function removeComment(TwitterComment $comment): void
    {
        $this->remove($comment);
    }

    // Remove comment from DB
    private function remove(TwitterComment $comment): void
    {
        $this->doctrine->getManager()->remove($comment);
        $this->doctrine->getManager()->flush();
    }

    private function save(TwitterComment $comment): TwitterComment
    {
        $this->doctrine->getManager()->persist($comment);
        $this->doctrine->getManager()->flush();

        return $comment;
    }
}
