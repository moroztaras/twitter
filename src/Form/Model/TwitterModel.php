<?php

namespace App\Form\Model;

use App\Entity\Twitter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterModel
{
    #[Assert\NotBlank]
    private ?string $text = null;

    private ?UploadedFile $photo;

    private ?string $video = null;

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getPhoto(): UploadedFile
    {
        return $this->photo;
    }

    public function setPhoto(?UploadedFile $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function setEntityTwitter(Twitter $twitter): self
    {
        return $this
            ->setText($twitter->getText() ?? null)
            ->setVideo($twitter->getVideo() ?? null)
        ;
    }
}
