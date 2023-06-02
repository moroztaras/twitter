<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class RecoverPasswordModel
{
    #[Assert\Email]
    public string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
