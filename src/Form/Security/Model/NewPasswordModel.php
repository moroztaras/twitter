<?php

namespace App\Form\Security\Model;

use Symfony\Component\Validator\Constraints as Assert;

class NewPasswordModel
{
    #[Assert\Length(min: 8, max: 50)]
    private string $plainPassword;

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): NewPasswordModel
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
