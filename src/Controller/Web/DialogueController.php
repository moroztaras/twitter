<?php

namespace App\Controller\Web;

use App\Components\Form\EntityDeleteForm;
use App\Entity\Dialogue;
use App\Entity\User;
use App\Manager\DialogueManager;
use App\Manager\FriendManager;
use App\Manager\MessageManager;
use App\Manager\UserProfileManager;
use App\Security\Voter\DialogueVoter;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('{_locale<%app.supported_locales%>}/dialogue')]
class DialogueController extends AbstractWebController
{
    public function __construct(
        private readonly DialogueManager $dialogueManager,
        private readonly MessageManager $messageManager,
        private readonly FriendManager $friendManager,
        private readonly RequestStack $requestStack,
        private readonly UserProfileManager $userProfileManager,
    ) {
    }

    #[Route('', name: 'web_user_dialogues_list')]
    public function dialogues(): Response
    {
        return $this->render('web/dialogue/index.html.twig', [
            'user' => $this->getUser(),
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
    #[IsGranted(DialogueVoter::IS_CREATOR, subject: 'uuid')]
    public function deleteDialogue(Request $request, Dialogue $dialogue, string $uuid): Response|RedirectResponse
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

    public function dialogueUserInfo(Dialogue $dialogue): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('block/dialogueUserInfo.html.twig', [
            'userReceiver' => $this->userProfileManager->getUserInfo($dialogue->getCreator() == $user ? $dialogue->getReceiver()->getId() : $dialogue->getCreator()->getId()),
            'numberMessages' => $this->messageManager->numberNotReadMessages($user, $dialogue->getId()),
            'uuidDialogue' => $dialogue->getUuid(),
        ]);
    }
}
