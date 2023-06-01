<?php

namespace App\Form\Model;

use App\Entity\TwitterComment;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterCommentModel
{
    #[Assert\NotBlank]
    private string $comment;

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function setEntityTwitterComment(TwitterComment $comment): void
    {
        $this->setComment($comment->getComment());
    }
}
