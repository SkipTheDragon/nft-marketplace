<?php

namespace App\Entity;

use App\Architecture\ENFTTypes;
use App\Repository\NFTRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NFTRepository::class)]
class NFT
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Blockchain::class, inversedBy: 'nfts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Blockchain $blockchain = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, AccountWallet>
     */
    #[ORM\ManyToMany(targetEntity: AccountWallet::class, inversedBy: 'nfts')]
    private Collection $owner;

    #[ORM\Column(length: 255)]
    private ?ENFTTypes $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $importedOn = null;

    public function __construct()
    {
        $this->owner = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBlockchain(): ?Blockchain
    {
        return $this->blockchain;
    }

    public function setBlockchain(?Blockchain $blockchain): static
    {
        $this->blockchain = $blockchain;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, AccountWallet>
     */
    public function getOwner(): Collection
    {
        return $this->owner;
    }

    public function addOwner(AccountWallet $owner): static
    {
        if (!$this->owner->contains($owner)) {
            $this->owner->add($owner);
        }

        return $this;
    }

    public function removeOwner(AccountWallet $owner): static
    {
        $this->owner->removeElement($owner);

        return $this;
    }

    public function getType(): ?ENFTTypes
    {
        return $this->type;
    }

    public function setType(ENFTTypes $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getImportedOn(): ?\DateTimeInterface
    {
        return $this->importedOn;
    }

    public function setImportedOn(\DateTimeInterface $importedOn): static
    {
        $this->importedOn = $importedOn;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
