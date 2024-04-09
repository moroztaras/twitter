<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Manager\DialogueManager;
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
}
