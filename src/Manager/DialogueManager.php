<?php

namespace App\Manager;

use App\Entity\Dialogue;
use App\Entity\User;
use App\Repository\DialogueRepository;
use Doctrine\Persistence\ManagerRegistry;

class DialogueManager
{
    public function __construct(
        private readonly DialogueRepository $dialogueRepository,
        private readonly ManagerRegistry $doctrine,
    ) {
    }

    public function allUserDialogs(User $user): array
    {
        return $this->dialogueRepository->dialoguesOfUser($user);
    }

    public function createNewDialogue(User $user, User $receiver): string
    {
        $dialogue = $this->dialogueRepository->xfindOneDialogue($user, $receiver);
        if ($dialogue) {
            return $dialogue->getUuid();
        }

        $dialogue = (new Dialogue())->setCreator($user)->setReceiver($receiver);

        $this->saveDialogue($dialogue);

        return $dialogue->getUuid();
    }

    private function saveDialogue(Dialogue $dialogue): void
    {
        $this->doctrine->getManager()->persist($dialogue);
        $this->doctrine->getManager()->flush();
    }
}
