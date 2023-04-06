<?php

namespace App\Form\Security\Model;

use Symfony\Component\Validator\Constraints as Assert;

class RecoverPasswordModel
{
    #[Assert\Email]
    public ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email)
    {
        $this->email = $email;
    }
}
