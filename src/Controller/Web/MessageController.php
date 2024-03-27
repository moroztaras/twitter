<?php

namespace App\Controller\Web;

use App\Entity\Dialogue;
use App\Entity\User;
use App\Form\MessageType;
use App\Manager\MessageManager;
use App\Model\MessageRequest;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale<%app.supported_locales%>}')]
class MessageController extends AbstractWebController
{
    public const MESSAGE_PAGE_LIMIT = 5;

    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route('/{uuid_dialogue}/messages', name: 'web_user_dialogue_messages_list', requirements: ['uuid' => Uuid::VALID_PATTERN])]
    #[ParamConverter('dialogue', class: Dialogue::class, options: ['mapping' => ['uuid_dialogue' => 'uuid']])]
    public function userDialogueMessagesList(Request $request, Dialogue $dialogue): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $messages = $this->paginator->paginate(
            $this->messageManager->messagesOfDialogue($dialogue->getId(), $user),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', self::MESSAGE_PAGE_LIMIT)
        );

        $model = new MessageRequest();
        $form = $this->createForm(MessageType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageManager->sendMessage($user, $dialogue, $model->getMessage());

            return $this->redirectToRoute('web_user_dialogue_messages_list', ['uuid_dialogue' => $dialogue->getUuid()]);
        }

        return $this->render('web/message/list.html.twig', [
            'user' => $user,
            'messages' => $messages,
            'form' => $form->createView(),
            'dialogue' => $dialogue,
        ]);
    }

    public function numberAllUnReadMessages(): Response
    {
        return $this->render('block/numberOfUnreadMessages.html.twig', [
            'numberMessages' => $this->messageManager->numberNotReadMessages($this->getUser()),
        ]);
    }
}
