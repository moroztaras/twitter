<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Manager\FriendManager;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends AbstractWebController
{
    public function __construct(private FriendManager $friendManager)
    {
    }

    public function userCover(User $user): Response
    {
        if (!$user) {
            return new Response();
        }
        //        $count_svistyns = $this->getDoctrine()->getRepository(Svistyn::class)->counterSvistynsByUser($user);

        return $this->render('block/cover.html.twig', [
            'user' => $user,
//            'count_svistyns' => $count_svistyns,
        ]);
    }

    public function following(User $user): Response
    {
        return $this->render('web/friend/count_followers.html.twig', [
            'count' => $this->friendManager->getCountFollowingsOfUser($user) ?? 0,
            'user' => $user,
        ]);
    }
}
