<?php

namespace App\Manager;

use App\Entity\User;
use App\Form\UserProfile\Model\UserProfileModel;
use Doctrine\Persistence\ManagerRegistry;

/**
 * UserProfileManager class.
 */
class UserProfileManager
{
    /**
     * UserProfileManager constructor.
     */
    public function __construct(
        private ManagerRegistry $doctrine,
    ) {
    }

    public function edit(UserProfileModel $userProfileModel, User $user): void
    {
        $user
            ->setFirstName($userProfileModel->getFirstName())
            ->setLastName($userProfileModel->getLastName())
            ->setBirthday($userProfileModel->getBirthday())
            ->setGender($userProfileModel->getGender())
            ->setCountry($userProfileModel->getCountry())
        ;

        $this->save($user);
    }

    // Save user in DB
    private function save(User $user): void
    {
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
    }
}
