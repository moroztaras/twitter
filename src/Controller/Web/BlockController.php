<?php

namespace App\Controller\Web;

use App\Entity\Twitter;
use App\Entity\User;
use App\Manager\DialogueManager;
use App\Manager\FriendManager;
use App\Manager\MessageManager;
use App\Manager\TwitterManager;
use App\Repository\TwitterCommentRepository;
use App\Repository\TwitterRepository;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends AbstractWebController
{
    public function __construct(
        private readonly FriendManager $friendManager,
        private readonly TwitterManager $twitterManager,
        private readonly DialogueManager $dialogueManager,
        private readonly MessageManager $messageManager,
        private readonly TwitterRepository $twitterRepository,
        private readonly TwitterCommentRepository $twitterCommentRepository,
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
        return new Response($this->twitterRepository->countReTwitters($twitter));
    }

    public function countCommentsOfTwitter(Twitter $twitter): Response
    {
        return new Response($this->twitterCommentRepository->countCommentsOfTwitter($twitter));
    }

    public function dialoguesList(): Response
    {
        $user = $this->getUser();

        return $this->render(
            'web/dialogue/list.html.twig', [
                'user' => $user,
                'dialogues' => $this->dialogueManager->allUserDialogs($user),
            ]);
    }

    public function numberAllUnReadMessages(): Response
    {
        return $this->render('block/numberOfUnreadMessages.html.twig', [
            'numberMessages' => $this->messageManager->numberNotReadMessages($this->getUser()),
        ]);
    }
}
