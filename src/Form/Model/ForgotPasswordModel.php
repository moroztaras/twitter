<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordModel
{
    #[Assert\NotBlank(message: 'Please enter an email')]
    #[Assert\Email]
    private string $email;

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
