<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Manager\FriendManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractWebController
{
    public function __construct(
        private FriendManager $friendManager,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function statusFriendShip(User $friend): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('web/friend/status.html.twig', [
            'status' => (bool) $this->friendManager->checkFriendShip($user, $friend),
        ]);
    }

    #[Route('/user/friend/{friend}/status/{status}/change', name: 'user_friend_status_change', methods: 'GET')]
    public function changeFriendShip(User $friend, bool $status): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $message = $this->friendManager->handleStatusChangeFriendship($user, $friend, $status);
        $this->requestStack->getSession()->getFlashBag()->add($message['status'], $message['message']);

        return $this->redirectToRoute('user_twitter_list', ['id' => $friend->getId()]);
    }

    #[Route('/user/{id}/following', name: 'user_list_following', requirements: ['id' => '\d+'], defaults: ['id' => null], methods: 'GET')]
    public function userListFollowing(User $user): Response
    {
        return $this->render('web/friend/following_list.html.twig', [
            'followings' => $this->friendManager->followingOfUser($user),
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/followers', name: 'user_list_followers', requirements: ['id' => '\d+'], defaults: ['id' => null], methods: 'GET')]
    public function userListFollowers(User $user): Response
    {
        return $this->render('web/friend/followers_list.html.twig', [
            'followers' => $this->friendManager->followersOfUser($user),
            'user' => $user,
        ]);
    }
}
