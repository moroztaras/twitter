<?php

namespace App\Entity;

use App\Repository\TwitterCommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TwitterCommentRepository::class)]
class TwitterComment
{
    use UuidEntity;
    use DateTimeEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'comment', type: 'string')]
    #[Assert\NotBlank]
    private string $comment;

    #[ORM\ManyToOne(targetEntity: Twitter::class)]
    #[ORM\JoinColumn(name: 'twitter_id', referencedColumnName: 'id')]
    private Twitter $twitter;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\Column(name: 'approved', type: 'boolean')]
    private bool $approved;

    /**
     * Twitter comment construct.
     */
    public function __construct()
    {
        $this->setApproved(true);
        $this->createUuid();
        $this->setDateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTwitter(): Twitter
    {
        return $this->twitter;
    }

    public function setTwitter(Twitter $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }
}
