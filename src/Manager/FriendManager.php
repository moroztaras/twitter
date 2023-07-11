<?php

namespace App\Manager;

use App\Entity\Friend;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class FriendManager
{
    public function __construct(
        private ManagerRegistry $doctrine,
    ) {
    }

    public function checkFriendShip(User $user, int $friend): bool
    {
        return (bool) true;
    }

    public function changeStatusFriendship(User $user, User $userFriend, bool $status): Friend
    {
        $friend = new Friend();
        $friend->setUser($user)
            ->setStatus($status)
            ->setFriend($userFriend)
        ;

        return $this->saveFriend($friend);
    }

    private function saveFriend(Friend $friend): Friend
    {
        $this->doctrine->getManager()->persist($friend);
        $this->doctrine->getManager()->flush();

        return $friend;
    }
}
