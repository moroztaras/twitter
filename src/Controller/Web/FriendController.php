<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Manager\FriendManager;
use App\Manager\UserProfileManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale<%app.supported_locales%>}')]
class FriendController extends AbstractWebController
{
    public function __construct(
        private readonly FriendManager $friendManager,
        private readonly RequestStack $requestStack,
        private readonly UserProfileManager $userProfileManager,
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

    #[Route('/user/friend/{friend}/status/{status}/change', name: 'web_user_friend_status_change', methods: 'GET')]
    public function changeFriendShip(User $friend, string $status): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $message = $this->friendManager->handleStatusChangeFriendship($user, $friend, $status);
        $this->requestStack->getSession()->getFlashBag()->add($message['status'], $message['message']);

        return $this->redirectToRoute('web_user_twitter_list', ['id' => $friend->getId()]);
    }

    #[Route('/user/{id}/following', name: 'web_user_list_following', requirements: ['id' => '\d+'], defaults: ['id' => null], methods: 'GET')]
    public function userListFollowing(User $user): Response
    {
        return $this->render('web/friend/following_list.html.twig', [
            'followings' => $this->friendManager->followingOfUser($user),
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/followers', name: 'web_user_list_followers', requirements: ['id' => '\d+'], defaults: ['id' => null], methods: 'GET')]
    public function userListFollowers(User $user): Response
    {
        return $this->render('web/friend/followers_list.html.twig', [
            'followers' => $this->friendManager->followersOfUser($user, true),
            'user' => $user,
        ]);
    }

    #[Route('/user/followers/requests', name: 'web_list_follower_requests', methods: 'GET')]
    public function listFollowerRequests(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('web/friend/follower_list_requests.html.twig', [
            'followers' => $this->friendManager->followersOfUser($user),
            'user' => $user,
        ]);
    }

    public function countRequestsFriendShip(): Response
    {
        return new Response($this->friendManager->getCountFollowersOfUser($this->getUser(), false) ?? 0);
    }

    public function friendUserInfo(int $idUser): Response
    {
        return $this->render('block/friendUserInfo.html.twig', [
            'user' => $this->userProfileManager->getUserInfo($idUser),
        ]);
    }
}
