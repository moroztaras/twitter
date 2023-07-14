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

    public function changeStatusFriendship(User $user, User $friend, bool $status): array
    {
        /** @var Friend $friendShip */
        $friendShip = $this->checkFriendShip($user, $friend);
        if (0 == $status) {
            if ($friendShip) {
                $this->removeFiend($friendShip);

                return ['status' => 'danger', 'message' => 'friend_remove'];
            } else {
                $newFriend = (new Friend())
                    ->setUser($user)
                    ->setFriend($friend)
                ;
                $this->saveFriend($newFriend);

                return ['status' => 'success', 'message' => 'application_has_been_sent'];
            }
        } else {
            $friendShip->setStatus(true);

            $this->saveFriend($friendShip);

            return ($status) ? ['success', 'application_has_been_successfully_verified'] : ['danger', 'application_has_been_canceled'];
        }
    }

    public function checkFriendShip(User $user, User $friend): Friend|null
    {
        return $this->doctrine->getRepository(Friend::class)->find(30);
    }

    private function removeFiend(Friend $friend): void
    {
        $this->doctrine->getManager()->remove($friend);
        $this->doctrine->getManager()->flush();
    }

    private function saveFriend(Friend $friend): Friend
    {
        $this->doctrine->getManager()->persist($friend);
        $this->doctrine->getManager()->flush();

        return $friend;
    }
}
