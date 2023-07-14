<?php

namespace App\Entity;

use App\Repository\FriendRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendRepository::class)]
class Friend
{
    use DateTimeEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'friends')]
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'friend_id', referencedColumnName: 'id')]
    private ?User $friend;

    // Status friendship
    #[ORM\Column(name: 'status')]
    private bool $status = false;

    public function __construct()
    {
        $this->setDateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFriend(): ?User
    {
        return $this->friend;
    }

    public function setFriend(?User $friend): self
    {
        $this->friend = $friend;

        return $this;
    }
}