<?php

namespace App\Entity;

use App\Architecture\EBlockchainType;
use App\Repository\BlockchainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlockchainRepository::class)]
class Blockchain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(unique: true)]
    private ?int $chainId = null;

    #[ORM\Column(length: 255)]
    private ?string $nativeToken = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(length: 255)]
    private ?EBlockchainType $type = null;

    /**
     * @var Collection<int, NFT>
     */
    #[ORM\OneToMany(targetEntity: NFT::class, mappedBy: 'blockchain', orphanRemoval: true)]
    private Collection $nfts;

    /**
     * @var Collection<int, RpcProvider>
     */
    #[ORM\OneToMany(targetEntity: RpcProvider::class, mappedBy: 'blockchain')]
    private Collection $rpcProviders;

    public function __construct()
    {
        $this->rpcProviders = new ArrayCollection();
        $this->nfts = new ArrayCollection();
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

    public function getChainId(): ?int
    {
        return $this->chainId;
    }

    public function setChainId(int $chainId): static
    {
        $this->chainId = $chainId;

        return $this;
    }

    public function getNativeToken(): ?string
    {
        return $this->nativeToken;
    }

    public function setNativeToken(string $nativeToken): static
    {
        $this->nativeToken = $nativeToken;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getType(): ?EBlockchainType
    {
        return $this->type;
    }

    public function setType(EBlockchainType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, NFT>
     */
    public function getNfts(): Collection
    {
        return $this->nfts;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, RpcProvider>
     */
    public function getRpcProviders(): Collection
    {
        return $this->rpcProviders;
    }

    public function addRpcProvider(RpcProvider $rpcProvider): static
    {
        if (!$this->rpcProviders->contains($rpcProvider)) {
            $this->rpcProviders->add($rpcProvider);
            $rpcProvider->setBlockchain($this);
        }

        return $this;
    }

    public function removeRpcProvider(RpcProvider $rpcProvider): static
    {
        if ($this->rpcProviders->removeElement($rpcProvider)) {
            // set the owning side to null (unless already changed)
            if ($rpcProvider->getBlockchain() === $this) {
                $rpcProvider->setBlockchain(null);
            }
        }

        return $this;
    }
}
