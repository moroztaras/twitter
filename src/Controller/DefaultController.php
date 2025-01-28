<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\FriendManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private readonly FriendManager $friendManager,
    ) {
    }

    #[Route('/', name: 'web_default_page')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // If user have followings
        if ($this->friendManager->getCountFollowingsOfUser($user)) {
            return $this->redirectToRoute('web_twitter_following');
        }

        return $this->redirectToRoute('web_twitter_list');
    }
}
