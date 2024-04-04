<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Manager\DialogueManager;
use App\Manager\FriendManager;
use App\Manager\MessageManager;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale<%app.supported_locales%>}/dialogue')]
class DialogueController extends AbstractWebController
{
    public function __construct(
        private readonly DialogueManager $dialogueManager,
        private readonly MessageManager $messageManager,
        private readonly FriendManager $friendManager
    ) {
    }

    #[Route('', name: 'web_user_dialogues_list')]
    public function dialogues(): Response
    {
        $user = $this->getUser();

        return $this->render('web/dialogue/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{uuid}', name: 'web_user_dialogue', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: 'GET')]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['uuid' => 'uuid']])]
    public function dialogue(User $user): Response
    {
        return $this->redirectToRoute('web_user_dialogue_messages_list', [
            'uuid_dialogue' => $this->dialogueManager->createNewDialogue($this->getUser(), $user),
        ]);

    }

    #[Route('/followings', name: 'web_dialogue_followings_list', methods: 'GET')]
    public function followingList(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('web/dialogue/following.html.twig', [
            'followings' => $this->friendManager->followingOfUser($user),
            'user' => $user,
        ]);
    }

    public function numberUnReadMessagesOfDialogue(int $dialogueId): Response
    {
        return $this->render('block/numberOfUnreadMessages.html.twig', [
            'numberMessages' => $this->messageManager->numberNotReadMessages($this->getUser(), $dialogueId),
       ]);
    }
}
