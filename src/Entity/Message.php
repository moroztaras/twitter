<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Message
{
    use UuidEntity;
    use DateTimeEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'sender_id', referencedColumnName: 'id')]
    private User $sender;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'receiver_id', referencedColumnName: 'id')]
    private User $receiver;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 5,
        max: 2048,
        minMessage: 'Message must be at least {{ limit }} characters long',
        maxMessage: 'Message cannot be longer than {{ limit }} characters'
    )]
    #[ORM\Column(name: 'message', type: 'string')]
    private string $message;

    #[ORM\ManyToOne(targetEntity: Dialogue::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'dialogue_id', referencedColumnName: 'id')]
    private Dialogue $dialogue;

    #[ORM\Column(name: 'status', type: 'boolean')]
    private bool $status = false;

    public function __construct()
    {
        $this->createUuid();
        $this->setDateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): User
    {
        return $this->sender;
    }

    public function setSender(User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): User
    {
        return $this->receiver;
    }

    public function setReceiver(User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDialogue(): Dialogue
    {
        return $this->dialogue;
    }

    public function setDialogue(Dialogue $dialogue): self
    {
        $this->dialogue = $dialogue;

        return $this;
    }
}
