<?php

namespace App\Controller\Web;

use App\Components\Form\EntityDeleteForm;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Manager\DialogueManager;
use App\Manager\MessageManager;
use App\Model\MessageRequest;
use App\Security\Voter\MessageVoter;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('{_locale<%app.supported_locales%>}')]
class MessageController extends AbstractWebController
{
    public const MESSAGE_PAGE_LIMIT = 5;

    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly DialogueManager $dialogueManager,
        private readonly PaginatorInterface $paginator,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[Route(path: '/dialogue/{uuid}/message/new', name: 'web_user_message_new', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: ['GET', 'POST'])]
    public function new(Request $request, string $uuid): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $dialogue = $this->dialogueManager->dialogue($uuid);

        $model = new MessageRequest();
        $form = $this->createForm(MessageType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageManager->sendMessage($user, $dialogue, $model->getMessage());

            return $this->redirectToRoute('web_user_message_new', ['uuid' => $uuid]);
        }

        return $this->render('web/message/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'dialogue' => $dialogue,
        ]);
    }

    #[Route(path: '/messages/{uuid}/edit', name: 'web_user_message_edit', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: ['GET', 'POST'])]
    #[IsGranted(MessageVoter::IS_SENDER, subject: 'uuid')]
    public function editMessage(Request $request, string $uuid): Response|RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Message $message */
        $message = $this->messageManager->getMessage($uuid);

        $model = (new MessageRequest())->setMessage($message->getMessage());
        $form = $this->createForm(MessageType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->messageManager->editMessage($message, $model->getMessage());

            return $this->redirectToRoute('web_user_message_new', $this->messageManager->dialogUuidByMessageUuid($uuid));
        }

        return $this->render('web/message/edit.html.twig', array_merge_recursive([
            'user' => $user,
            'form' => $form->createView()],
            $this->messageManager->dialogUuidByMessageUuid($uuid)
        ));
    }

    #[Route(path: '/messages/{uuid}/delete', name: 'web_user_message_delete', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: ['GET', 'POST'])]
    #[IsGranted(MessageVoter::IS_SENDER, subject: 'uuid')]
    public function deleteMessage(Request $request, string $uuid): Response|RedirectResponse
    {
        $form = $this->createForm(EntityDeleteForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dialogUuid = $this->messageManager->dialogUuidByMessageUuid($uuid);

            $this->messageManager->removeMessage($uuid);
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'message_was_deleted_successfully');

            return $this->redirectToRoute('web_user_message_new', $dialogUuid);
        }

        return $this->render('web/message/delete.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'uuid' => $this->messageManager->dialogUuidByMessageUuid($uuid),
        ]);
    }

    public function list(Request $request, string $uuid): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $dialogue = $this->dialogueManager->dialogue($uuid);

        return $this->render('web/message/list.html.twig', [
            'user' => $user,
            'dialogue' => $dialogue,
            'messages' => $this->paginator->paginate(
                $this->messageManager->messagesOfDialogue($dialogue->getId(), $user),
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', self::MESSAGE_PAGE_LIMIT)
            ),
        ]);
    }
}
