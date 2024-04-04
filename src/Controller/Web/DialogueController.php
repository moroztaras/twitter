<?php

namespace App\Controller\Web;

use App\Components\Form\EntityDeleteForm;
use App\Entity\Dialogue;
use App\Entity\User;
use App\Manager\DialogueManager;
use App\Manager\FriendManager;
use App\Manager\MessageManager;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('{_locale<%app.supported_locales%>}/dialogue')]
class DialogueController extends AbstractWebController
{
    public function __construct(
        private readonly DialogueManager $dialogueManager,
        private readonly MessageManager $messageManager,
        private readonly FriendManager $friendManager,
        private readonly RequestStack $requestStack,
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
    public function dialogue(User $user): RedirectResponse
    {
        return $this->redirectToRoute('web_user_dialogue_messages_list', [
            'uuid' => $this->dialogueManager->createNewDialogue($this->getUser(), $user),
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

    #[Route('/{uuid}/delete', name: 'web_user_dialogue_delete', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: ['POST', 'GET'])]
    #[ParamConverter('dialogue', class: Dialogue::class, options: ['mapping' => ['uuid' => 'uuid']])]
    public function deleteDialogue(Request $request, Dialogue $dialogue): Response|RedirectResponse
    {
        $form = $this->createForm(EntityDeleteForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dialogueManager->removeDialogue($dialogue);
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'dialogue_was_deleted');

            return $this->redirectToRoute('web_user_dialogues_list');
        }

        return $this->render('web/dialogue/delete.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'uuid' => $dialogue->getUuid(),
        ]);
    }

    public function numberUnReadMessagesOfDialogue(int $dialogueId): Response
    {
        return $this->render('block/numberOfUnreadMessages.html.twig', [
            'numberMessages' => $this->messageManager->numberNotReadMessages($this->getUser(), $dialogueId),
       ]);
    }
}
