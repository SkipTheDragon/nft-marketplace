<?php

namespace App\Entity;

use App\Architecture\EAccountWallet;
use App\Repository\AccountWalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountWalletRepository::class)]
class AccountWallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?EAccountWallet $type = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'wallets')]
    #[ORM\JoinColumn('account_data', nullable: true)]
    private ?Account $account = null;

    /**
     * @var Collection<int, NFT>
     */
    #[ORM\ManyToMany(targetEntity: NFT::class, mappedBy: 'owner')]
    private Collection $nfts;

    public function __construct()
    {
        $this->nfts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?EAccountWallet
    {
        return $this->type;
    }

    public function setType(EAccountWallet $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection<int, NFT>
     */
    public function getNfts(): Collection
    {
        return $this->nfts;
    }

    public function addNft(NFT $nft): static
    {
        if (!$this->nfts->contains($nft)) {
            $this->nfts->add($nft);
            $nft->addOwner($this);
        }

        return $this;
    }

    public function removeNft(NFT $nft): static
    {
        if ($this->nfts->removeElement($nft)) {
            $nft->removeOwner($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->address;
    }
}
