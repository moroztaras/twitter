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
        $messages = $this->messageRepository->allMessagesOfDialogue($dialogueId, $user->getUuid());

        $statusFlush = false;
        /** @var Message $message */
        foreach ($messages as $message) {
            if ($message->getReceiver() === $user && (false == $message->getStatus())) {
                $statusFlush = true;
                $message->setStatus(true);
                $this->doctrine->getManager()->persist($message);
            }
        }
        if ($statusFlush) {
            $this->doctrine->getManager()->flush();
        }

        return $messages;
    }
    public function getMessage(string $uuid): Message
    {
        return $this->messageRepository->getMessageByUuid($uuid);
    }

    public function sendMessage(User $user, Dialogue $dialogue, string $message): void
    {
        $message = (new Message())
            ->setMessage($message)
            ->setDialogue($dialogue)
            ->setSender($user)
            ->setReceiver(($dialogue->getCreator()->getUuid() === $user->getUuid()) ? $dialogue->getReceiver() : $user);

        $this->saveMessage($message);
    }

    public function editMessage(Message $message, string $textMessage): Message
    {
        $message->setMessage($textMessage);

        $this->saveMessage($message);

        return $message;
    }

    public function removeMessage(string $uuid): void
    {
        $message = $this->messageRepository->getMessageByUuid($uuid);

        $this->messageRepository->removeAndCommit($message);
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
