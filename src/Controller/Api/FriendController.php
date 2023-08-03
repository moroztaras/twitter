<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Manager\FriendManager;
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

    #[Route('api/user/following', name: 'api_user_list_following', methods: 'GET')]
    public function userListFollowing(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        return $this->json([
            'followings' => $this->friendManager->followingOfUser($user)], Response::HTTP_OK, [], ['following' => true]
        );
    }
}
