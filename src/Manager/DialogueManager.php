<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\DialogueRepository;

class DialogueManager
{
    public function __construct(
        private readonly DialogueRepository $dialogueRepository,
    ) {
    }

    public function allUserDialogs(User $user): array
    {
        return $this->dialogueRepository->dialoguesOfUser($user);
    }
}
