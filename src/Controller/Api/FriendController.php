<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Manager\FriendManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends ApiController
{
    public function __construct(
        private readonly FriendManager $friendManager,
    ) {
    }

    #[Route('api/user/followings', name: 'api_user_list_followings', methods: 'GET')]
    public function userListFollowing(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        return $this->json([
            'followings' => $this->friendManager->followingOfUser($user)], Response::HTTP_OK, [], ['following' => true]
        );
    }

    #[Route('api/user/{uuid}/followings', name: 'api_user_show_list_followings', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: 'GET')]
    public function showListFollowingOfUser(Request $request, User $user): JsonResponse
    {
        $this->getCurrentUser($request);

        return $this->json([
            'followings' => $this->friendManager->followingOfUser($user)], Response::HTTP_OK, [], ['following' => true]
        );
    }

    #[Route('api/user/followers', name: 'api_user_list_followers', methods: 'GET')]
    public function userListFollowers(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        return $this->json([
            'followers' => $this->friendManager->followersOfUser($user, true)], Response::HTTP_OK, [], ['followers' => true]
        );
    }

    #[Route('api/user/followers/requests', name: 'api_user_followers_list_requests', methods: 'GET')]
    public function listRequestsFromFollowers(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        return $this->json([
            'requests' => $this->friendManager->followersOfUser($user)], Response::HTTP_OK, [], ['request' => true]
        );
    }
}
