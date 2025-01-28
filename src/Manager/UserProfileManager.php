<?php

namespace App\Manager;

use App\Entity\User;
use App\Form\Model\UserProfileModel;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * UserProfileManager class.
 */
class UserProfileManager
{
    /**
     * UserProfileManager constructor.
     */
    public function __construct(
        private string $avatarDir,
        private string $coverDir,
        private ManagerRegistry $doctrine,
        private FileManager $fileManager,
        private UserRepository $userRepository,
    ) {
    }

    public function edit(
        UserProfileModel $userProfileModel,
        User $user,
        ?UploadedFile $avatar = null,
        ?UploadedFile $cover = null,
    ): void {
        if ($avatar) {
            $user->setAvatar($this->fileManager->upload($avatar, $this->avatarDir));
        }
        if ($cover) {
            $user->setCover($this->fileManager->upload($cover, $this->coverDir));
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

    public function getUserInfo(int $idUser): array
    {
        return $this->userRepository->findOneById($idUser);
    }

    // Save user in DB
    private function save(User $user): void
    {
        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
    }
}
