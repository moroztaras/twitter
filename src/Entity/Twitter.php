<?php

namespace App\Entity;

use App\Repository\TwitterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TwitterRepository::class)]
class Twitter
{
    use UuidEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(
        min: 1,
        max: 1024,
        minMessage: 'The text must be at least 1 characters',
        maxMessage: 'The text must be no more than 50 characters'
    )]
    #[ORM\Column(name: 'text')]
    private string $text;

    #[ORM\Column(name: 'video', nullable: true)]
    private ?string $video;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'twitters')]
    private ?User $user;

    // The number of post views
    #[ORM\Column(name: 'views')]
    private int $views = 0;

    // View post for display
    #[ORM\Column(name: 'status')]
    private bool $status = true;

    #[ORM\Column(name: 'photo', type: 'string', nullable: true)]
    private ?string $photo;

    #[ORM\OneToMany(mappedBy: 'twitter', targetEntity: TwitterComment::class, cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['id' => 'DESC'])]
    private Collection $comments;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $updatedAt;

    /**
     * Twitter construct.
     */
    public function __construct(UuidInterface $uuid = null)
    {
        if (!$uuid) {
            $this->createUuid();
        } else {
            $this->uuid = $uuid;
        }
        $this->comments = new ArrayCollection();

        $this->setCreatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(TwitterComment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTwitter($this);
        }

        return $this;
    }

    public function removeComment(TwitterComment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTwitter() === $this) {
                $comment->setTwitter(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    // The date is set before the data will persist to the database.
    #[ORM\PrePersist]
    public function setUpdatedValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
