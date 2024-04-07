<?php

namespace App\Security\Voter;

use App\Repository\DialogueRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DialogueVoter extends Voter
{
    final public const IS_CREATOR = 'IS_CREATOR';

    public function __construct(private readonly DialogueRepository $dialogueRepository)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // Validation of the passed attribute
        if (self::IS_CREATOR !== $attribute) {
            return false;
        }

        // Check. If the object is a string type and length is greater than zero
        return is_string($subject) && (strlen($subject) > 0);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return $this->dialogueRepository->existsCreatorDialogueByUuid((string) $subject, $token->getUser());
    }
}
