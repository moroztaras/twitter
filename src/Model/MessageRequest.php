<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class MessageRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 1000)]
    private string $message;

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
