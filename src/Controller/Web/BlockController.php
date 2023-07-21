<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Manager\FriendManager;
use App\Manager\TwitterManager;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends AbstractWebController
{
    public function __construct(
        private FriendManager $friendManager,
        private TwitterManager $twitterManager
    ) {
    }

    public function userCover(User $user): Response
    {
        if (!$user) {
            return new Response();
        }

        return $this->render('block/cover.html.twig', [
            'user' => $user,
            'following' => $this->friendManager->getCountFollowingsOfUser($user) ?? 0,
            'followers' => $this->friendManager->getCountFollowersOfUser($user) ?? 0,
            'twitters' => $this->twitterManager->getCountTwittersOfUser($user) ?? 0,
        ]);
    }
}
