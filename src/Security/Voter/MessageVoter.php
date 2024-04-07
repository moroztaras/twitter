<?php

namespace App\Security\Voter;

use App\Repository\MessageRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MessageVoter extends Voter
{
    final public const IS_SENDER = 'IS_SENDER';

    public function __construct(private readonly MessageRepository $messageRepository)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // Validation of the passed attribute
        if (self::IS_SENDER !== $attribute) {
            return false;
        }

        // Check. If the object is a string type and length is greater than zero
        return is_string($subject) && (36 === strlen($subject));
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return $this->messageRepository->existsSenderOfMessageByUuid((string) $subject, $token->getUser());
    }
}
