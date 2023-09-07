<?php

namespace App\Form\Model;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserProfileSecurityModel
{
    //    #[Assert\Length(min: '8', max: '50')]
    private string $password;

    #[Assert\Length(min: '8', max: '50')]
    private string $newPassword;

    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    public function setUser(User $user): self
    {
        $this->email = $user->getEmail();

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return strtolower($this->email);
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }
}
