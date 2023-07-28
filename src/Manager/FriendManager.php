<?php

namespace App\Manager;

use App\Entity\Friend;
use App\Entity\User;
use App\Repository\FriendRepository;
use Doctrine\Persistence\ManagerRegistry;

class FriendManager
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private readonly FriendRepository $friendRepository,
    ) {
    }

    public function handleStatusChangeFriendship(User $user, User $friend, bool $status): array
    {
        /** @var Friend $friendShip */
        $friendShip = $this->checkFriendShip($user, $friend);

        if (0 == $status) {
            if ($friendShip) {
                $this->removeFriend($friendShip);

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
        return $this->friendRepository->findOneByUsers($user, $friend);
    }

    public function getCountFollowingsOfUser(User $user): int
    {
        return $this->friendRepository->countFollowingsOfOneUser($user);
    }

    public function getCountFollowersOfUser(User $user, $status = false): int
    {
        return $this->friendRepository->countFollowersOfOneUser($user, $status);
    }

    public function followingOfUser(User $user): array
    {
        return $this->friendRepository->allFollowingsOfOneUser($user);
    }

    public function followersOfUser(User $user, $status = false): array
    {
        return $this->friendRepository->allFollowersOfOneUserByStatus($user, $status);
    }

    private function removeFriend(Friend $friend): void
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
