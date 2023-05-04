<?php

namespace App\Form\UserProfile\Model;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UserProfileModel
{
    #[Assert\NotBlank()]
    private string $firstName;

    #[Assert\NotBlank()]
    private string $lastName;

    #[Assert\NotBlank()]
    private string $email;

    #[Assert\NotBlank()]
    private \DateTimeInterface $birthday;

    #[Assert\NotBlank()]
    private string $gender;

    #[Assert\Country()]
    private string $country;

    private UploadedFile $avatar;

    private UploadedFile $cover;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAvatar(): UploadedFile
    {
        return $this->avatar;
    }

    public function setAvatar(?UploadedFile $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCover(): UploadedFile
    {
        return $this->cover;
    }

    public function setCover(?UploadedFile $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function setUser(User $user): self
    {
        return $this
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setEmail($user->getEmail())
            ->setBirthday($user->getBirthday())
            ->setGender($user->getGender())
        ;
    }
}
