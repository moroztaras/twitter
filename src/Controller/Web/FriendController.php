<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Manager\FriendManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractWebController
{
    public function __construct(
        private FriendManager $friendManager,
    ) {
    }

    public function statusFriendShip(int $friend): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('web/friend/status.html.twig', [
            'status' => $this->friendManager->checkFriendShip($user, $friend),
        ]);
    }

    #[Route('/user/friend/{friend}/status/{status}/change', name: 'user_friend_status_change', methods: 'GET')]
    public function changeFriendShip(User $friend, bool $status) : Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->friendManager->changeStatusFriendship($user, $friend, $status);

        return $this->redirectToRoute('user_twitter_list', ['id' => $friend->getId()]);
    }
}
