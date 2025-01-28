<?php

namespace App\Controller\Api;

use App\Entity\Dialogue;
use App\Entity\User;
use App\Manager\DialogueManager;
use App\Response\SuccessResponse;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DialogueController extends ApiController
{
    public function __construct(
        private readonly DialogueManager $dialogueManager,
    ) {
    }

    #[Route('api/user/dialogues', name: 'api_dialogues_list', methods: 'GET')]
    public function dialoguesList(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        return $this->json([
            'dialogues' => $this->dialogueManager->allUserDialogs($user),
        ], Response::HTTP_OK);
    }

    #[Route('api/user/dialogue/{uuid_receiver}', name: 'api_user_dialogue_create', requirements: ['uuid_receiver' => Uuid::VALID_PATTERN], methods: 'POST')]
    #[ParamConverter('receiver', class: User::class, options: ['mapping' => ['uuid_receiver' => 'uuid']])]
    public function create(Request $request, User $receiver): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        return $this->json([
            'dialogue' => $this->dialogueManager->createNewDialogue($user, $receiver),
        ], Response::HTTP_OK);
    }

    #[Route('api/user/dialogue/{uuid}', name: 'api_user_dialogue_delete', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: 'DELETE')]
    #[ParamConverter('dialogue', class: Dialogue::class, options: ['mapping' => ['uuid' => 'uuid']])]
    public function delete(Request $request, Dialogue $dialogue): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        $this->dialogueManager->removeDialogue($dialogue);

        return new SuccessResponse();
    }
}
