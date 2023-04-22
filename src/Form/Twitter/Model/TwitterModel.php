<?php

namespace App\Form\Twitter\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TwitterModel
{
    #[Assert\NotBlank]
    private $text = null;

    private string $video;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText($text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getVideo(): string
    {
        return $this->video;
    }

    public function setVideo($video): self
    {
        $this->video = $video;

        return $this;
    }
}
