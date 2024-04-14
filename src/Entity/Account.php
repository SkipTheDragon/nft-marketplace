<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: '`account`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Account implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $registeredAt = null;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    /**
     * @var Collection<int, AccountWallet>
     */
    #[ORM\OneToMany(targetEntity: AccountWallet::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $wallets;

    /**
     * @var Collection<int, AccountSession>
     */
    #[ORM\OneToMany(targetEntity: AccountSession::class, mappedBy: 'account', orphanRemoval: true)]
    private Collection $sessions;

    public function __construct()
    {
        $this->registeredAt = new \DateTimeImmutable();
        $this->wallets = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeImmutable $registeredAt): static
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @see AccountInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every account at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {}

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, AccountWallet>
     */
    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    public function addWallet(AccountWallet $wallet): static
    {
        if (!$this->wallets->contains($wallet)) {
            $this->wallets->add($wallet);
            $wallet->setAccount($this);
        }

        return $this;
    }

    public function removeWallet(AccountWallet $wallet): static
    {
        if ($this->wallets->removeElement($wallet)) {
            // set the owning side to null (unless already changed)
            if ($wallet->getAccount() === $this) {
                $wallet->setAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AccountSession>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(AccountSession $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setAccount($this);
        }

        return $this;
    }

    public function removeSession(AccountSession $session): static
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getAccount() === $this) {
                $session->setAccount(null);
            }
        }

        return $this;
    }

}
