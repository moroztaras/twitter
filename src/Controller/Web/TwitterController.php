<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Manager\TwitterManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twitter')]
class TwitterController extends AbstractController
{
    public function __construct(
        private TwitterManager $twitterManager
    ) {
    }

    // List all twitter of user
    #[Route('/list', name: 'web_twitter_list')]
    public function list(): Response
    {
        $user = $this->getUser();

        return $this->render('web/twitter/list.html.twig', [
            'user' => $user,
            'twitters' => $this->twitterManager->list($user),
        ]);
    }
}
