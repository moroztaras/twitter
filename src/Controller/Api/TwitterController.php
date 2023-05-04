<?php

namespace App\Controller\Api;

use App\Entity\Twitter;
use App\Entity\User;
use App\Exception\Api\BadRequestJsonHttpException;
use App\Manager\TwitterManager;
use App\Response\SuccessResponse;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TwitterController.
 */
#[Route('api/twitter', name: 'api_twitter')]
class TwitterController extends ApiController
{
    public function __construct(
        private TwitterManager $twitterManager,
    ) {
    }

    // List twitters of user
    #[Route('', name: 'api_twitter_list', methods: 'GET')]
    public function list(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);
        $page = $request->query->get('page', 1);

        return $this->json([
            'page' => $page,
            'twitters' => $this->twitterManager->getTwitterPageByUserId($user->getId(), (int) $page)
        ], Response::HTTP_OK);
    }

    // Create new twitter
    #[Route('', name: 'api_twitter_create', methods: 'POST')]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser($request);

        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json(['twitter' => $this->twitterManager->new($content, $user)], Response::HTTP_OK);
    }

    // Show twitter
    #[Route('/{uuid}', name: 'api_twitter_show', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: 'GET')]
    public function show(Request $request, Twitter $twitter): JsonResponse
    {
        $this->getCurrentUser($request);

        return $this->json(['twitter' => $this->twitterManager->show($twitter)], Response::HTTP_OK);
    }

    // Edit twitter
   #[Route('/{uuid}', name: 'api_twitter_update', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: 'PUT')]
    public function update(Request $request, Twitter $twitter): JsonResponse
    {
        $this->getCurrentUser($request);

        if (!($content = $request->getContent())) {
            throw new BadRequestJsonHttpException('Bad Request.');
        }

        return $this->json([
            'twitter' => $this->twitterManager->edit($content, $twitter)],
            Response::HTTP_OK
        );
    }

    // Delete twitter
    #[Route('/{uuid}', name: 'api_twitter_delete', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: 'DELETE')]
    public function delete(Twitter $twitter): SuccessResponse
    {
        $this->twitterManager->remove($twitter);

        return new SuccessResponse();
    }
}
