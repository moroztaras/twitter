<?php

namespace App\Controller\Web;

use App\Manager\MessageManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale<%app.supported_locales%>}/dialogue')]
class DialogueController extends AbstractWebController
{
    public function __construct(private readonly MessageManager $messageManager)
    {
    }

    #[Route('', name: 'web_user_dialogues_list')]
    public function dialogues(): Response
    {
        $user = $this->getUser();

        return $this->render('web/message/index.html.twig', [
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
