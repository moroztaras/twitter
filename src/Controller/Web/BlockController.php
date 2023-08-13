<?php

namespace App\Controller\Web;

use App\Entity\Twitter;
use App\Entity\User;
use App\Manager\FriendManager;
use App\Manager\TwitterManager;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends AbstractWebController
{
    public function __construct(
        private readonly FriendManager $friendManager,
        private readonly TwitterManager $twitterManager
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
            'followers' => $this->friendManager->getCountFollowersOfUser($user, true) ?? 0,
            'twitters' => $this->twitterManager->getCountTwittersOfUser($user) ?? 0,
        ]);
    }

    public function countReTwitters(Twitter $twitter): Response
    {
        return new Response('0');
    }
}
