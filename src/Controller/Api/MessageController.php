<?php

namespace App\Controller\Api;

use App\Entity\Dialogue;
use App\Entity\User;
use App\Manager\MessageManager;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends ApiController
{
    public const MESSAGE_PAGE_LIMIT = 5;

    public function __construct(
        private readonly MessageManager $messageManager,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    #[Route('api/dialogue/{uuid}/messages', name: 'api_user_dialogue_messages_list', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: ['GET'])]
    #[ParamConverter('dialogue', class: Dialogue::class, options: ['mapping' => ['uuid' => 'uuid']])]
    public function MessagesListOfDialogue(Request $request, Dialogue $dialogue): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        $page = $request->query->getInt('page', 1);

        return $this->json([
            'page' => $page,
            'messages' => $this->paginator->paginate(
                $this->messageManager->messagesOfDialogue($dialogue->getId(), $user),
                $page,
                $request->query->getInt('limit', self::MESSAGE_PAGE_LIMIT)
            ),
        ], Response::HTTP_OK);
    }
}
