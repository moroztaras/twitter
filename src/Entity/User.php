<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    use UuidEntity;
    use DateTimeEntity;

    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email]
    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[Assert\Length(
        min: 8,
        max: 50,
        minMessage: 'Password must be at least 8 characters',
        maxMessage: 'The password must be no more than 50 characters'
    )]
    #[ORM\Column(name: 'password')]
    private string $password;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column(name: 'status')]
    private bool $status = false;

    #[ORM\Column(name: 'gender', length: 10)]
    private string $gender;

    #[ORM\Column(name: 'country', length: 256)]
    private string $country;

    #[ORM\Column(name: 'avatar', type: 'string', nullable: true)]
    private ?string $avatar;

    #[ORM\Column(name: 'cover', type: 'string', nullable: true)]
    private ?string $cover;

    #[ORM\Column(name: 'roles', type: Types::JSON)]
    private array $roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Twitter::class, cascade: ['persist'])]
    private Collection $twitters;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Friend::class, cascade: ['persist'])]
    private Collection $friends;

    #[ORM\Column(name: 'token_recover', length: 256, nullable: true)]
    private ?string $tokenRecover = null;

    #[ORM\Column(name: 'api_key', unique: true)]
    private string $apiKey;

    /**
     * User construct.
     */
    public function __construct()
    {
        $this->twitters = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->setRoles([self::ROLE_USER]);
        $this->createUuid();
        $this->setDateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): User
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): User
    {
        $this->status = $status;

        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): User
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): User
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getTokenRecover(): ?string
    {
        return $this->tokenRecover;
    }

    public function setTokenRecover(?string $tokenRecover): self
    {
        $this->tokenRecover = $tokenRecover;

        return $this;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return Collection<Twitter>
     */
    public function getTwitters(): Collection
    {
        return $this->twitters;
    }

    /**
     * @param Collection<Twitter> $twitters
     *
     * @return $this
     */
    public function setTwitters(Collection $twitters): self
    {
        $this->twitters = $twitters;

        return $this;
    }

    public function addTwitter(Twitter $twitter): self
    {
        if (!$this->twitters->contains($twitter)) {
            $this->twitters[] = $twitter;
            $twitter->setUser($this);
        }

        return $this;
    }

    public function removeTwitter(Twitter $twitter): self
    {
        if ($this->twitters->contains($twitter)) {
            $this->twitters->removeElement($twitter);
            // set the owning side to null (unless already changed)
            if ($twitter->getUser() === $this) {
                $twitter->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<Friend>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    /**
     * @param Collection<Friend> $friends
     *
     * @return $this
     */
    public function setFriends(Collection $friends): self
    {
        $this->friends = $friends;

        return $this;
    }

    public function addFriend(Twitter $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
            $friend->setUser($this);
        }

        return $this;
    }

    public function removeFriend(Friend $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
            // set the owning side to null (unless already changed)
            if ($friend->getUser() === $this) {
                $friend->setUser(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->getUuid();
    }
}
