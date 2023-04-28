<?php

namespace App\Manager;

use App\Entity\User;
use App\Form\UserProfile\Model\UserProfileModel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        private FileManager $fileManager,
    ) {
    }

    public function edit(UserProfileModel $userProfileModel, User $user, UploadedFile $file = null, $pathToDirectory): void
    {
        if ($file) {
            $user->setAvatar($this->fileManager->upload($file, $pathToDirectory));
        }
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
