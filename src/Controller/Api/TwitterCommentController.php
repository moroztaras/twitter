<?php

namespace App\Controller\Api;

use App\Entity\Twitter;
use App\Entity\TwitterComment;
use App\Exception\Api\BadRequestJsonHttpException;
use App\Exception\Expected\TwitterNotFoundException;
use App\Manager\TwitterCommentManager;
use App\Repository\TwitterCommentRepository;
use App\Response\SuccessResponse;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TwitterCommentController.
 */
class TwitterCommentController extends ApiController
{
    public function __construct(
        private TwitterCommentManager $twitterCommentManager,
        private TwitterCommentRepository $commentRepository,
        private PaginatorInterface $paginator,
    ) {
    }

    // List comments of twitter
    #[Route('api/twitter/{uuid_twitter}/comments/page={page}', name: 'api_twitter_comments_list', requirements: ['uuid_twitter' => Uuid::VALID_PATTERN], methods: 'GET')]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['uuid_twitter' => 'uuid']])]
    public function list(Request $request, Twitter $twitter, int $page): JsonResponse
    {
        $this->getCurrentUser($request);

        if (!$twitter) {
            throw new TwitterNotFoundException();
        }

        return $this->json(
            [
                'twitter-comments' => $this->paginator->paginate(
                    $this->commentRepository->getlistCommentsByTwitter($twitter),
                    $request->query->getInt('page', $page),
                    $this->getParameter('pagination_limit')
                ),
            ],
            Response::HTTP_OK);
    }

    // Show one comment
    #[Route('api/twitter/{uuid_twitter}/comment/{uuid_comment}', name: 'api_twitter_comment_show',
        requirements: ['uuid_twitter' => Uuid::VALID_PATTERN, 'uuid_comment' => Uuid::VALID_PATTERN], methods: 'GET')]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['uuid_twitter' => 'uuid']])]
    #[ParamConverter('comment', class: TwitterComment::class, options: ['mapping' => ['uuid_comment' => 'uuid']])]
    public function show(Request $request, TwitterComment $comment): JsonResponse
    {
        $this->getCurrentUser($request);

        return $this->json(['twitter-comment' => $comment]);
    }

    // New comment for twitter
    #[Route('/api/twitter/{uuid_twitter}/comment', name: 'api_twitter_comment_new', methods: 'POST')]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['uuid_twitter' => 'uuid']])]
    public function create(Request $request, Twitter $twitter): JsonResponse
    {
        $user = $this->getCurrentUser($request);

        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json([
            'twitter-comment' => $this->twitterCommentManager->new($content, $user, $twitter),
        ], Response::HTTP_OK);
    }

    // Edit comment of twitter
    #[Route('api/twitter/{uuid_twitter}/comment/{uuid_comment}', name: 'api_twitter_comment_edit',
        requirements: ['uuid_twitter' => Uuid::VALID_PATTERN, 'uuid_comment' => Uuid::VALID_PATTERN], methods: 'PUT')]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['uuid_twitter' => 'uuid']])]
    #[ParamConverter('comment', class: TwitterComment::class, options: ['mapping' => ['uuid_comment' => 'uuid']])]
    public function edit(Request $request, Twitter $twitter, TwitterComment $comment): JsonResponse
    {
        $user = $this->getCurrentUser($request);

        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json([
            'twitter-comment' => $this->twitterCommentManager->edit($content, $comment),
        ], Response::HTTP_OK);
    }

    // Remove comment of twitter
    #[Route('api/twitter/{uuid_twitter}/comment/{uuid_comment}', name: 'api_twitter_comment_delete',
        requirements: ['uuid_twitter' => Uuid::VALID_PATTERN, 'uuid_comment' => Uuid::VALID_PATTERN], methods: 'DELETE')]
    #[ParamConverter('twitter', class: Twitter::class, options: ['mapping' => ['uuid_twitter' => 'uuid']])]
    #[ParamConverter('comment', class: TwitterComment::class, options: ['mapping' => ['uuid_comment' => 'uuid']])]
    public function delete(Request $request, Twitter $twitter, TwitterComment $comment): JsonResponse
    {
        $user = $this->getCurrentUser($request);

        $this->twitterCommentManager->removeComment($comment);

        return new SuccessResponse();
    }
}
