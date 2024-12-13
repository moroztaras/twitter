<?php

namespace App\Controller\Api;

use App\Entity\Dialogue;
use App\Entity\User;
use App\Manager\MessageManager;
use App\Response\SuccessResponse;
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
    public function messagesListOfDialogue(Request $request, Dialogue $dialogue): JsonResponse
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

    #[Route('api/message/{uuid}', name: 'api_message_show', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: ['GET'])]
    public function message(Request $request, string $uuid): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        return $this->json(['message' => $this->messageManager->getMessage($uuid)], Response::HTTP_OK);
    }

    #[Route('api/message/{uuid}', name: 'api_message_delete', requirements: ['uuid' => Uuid::VALID_PATTERN], methods: ['DELETE'])]
    public function delete(Request $request, string $uuid): JsonResponse
    {
        /** @var User $user */
        $user = $this->getCurrentUser($request);

        $this->messageManager->removeMessage($uuid);

        return new SuccessResponse();
    }
}
