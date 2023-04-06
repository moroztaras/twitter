<?php

namespace App\Form\Security\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Forgot
{
    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @Assert\Email
     */
    private $email;

    public function setEmail(string $email): Forgot
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
