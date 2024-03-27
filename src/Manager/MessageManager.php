<?php

namespace App\Manager;

use App\Entity\Dialogue;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageManager
{
    public function __construct(
        private readonly MessageRepository $messageRepository,
        private readonly ManagerRegistry $doctrine,
    ) {
    }

    public function messagesOfDialogue(int $dialogueId, User $user): array
    {
        return $this->messageRepository->allMessagesOfDialogue($dialogueId, $user->getUuid());
    }

    public function sendMessage(User $user, Dialogue $dialogue, string $message): void
    {
        $message = (new Message())
            ->setMessage($message)
            ->setDialogue($dialogue)
            ->setSender($user)
            ->setReceiver(($dialogue->getCreator() === $user) ? $dialogue->getReceiver() : $user);

        $this->saveMessage($message);
    }

    private function saveMessage(Message $message): void
    {
        $this->doctrine->getManager()->persist($message);
        $this->doctrine->getManager()->flush();
    }

    public function numberNotReadMessages(User $user, int $dialogueId = null): int
    {
        return $this->messageRepository->numberNotReadMessages($user, $dialogueId);
    }
}
