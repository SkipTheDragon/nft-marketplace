<?php

namespace App\Entity;

use App\Repository\NFTImportItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NFTImportItemRepository::class)]
#[ORM\Table(name: 'nft_import_queue')]
class NFTImportItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $metadataUri = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $insertedAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $importedAt = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?NFT $nft = null;

    public function __construct()
    {
        $this->insertedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMetadataUri(): ?string
    {
        return $this->metadataUri;
    }

    public function setMetadataUri(string $metadataUri): static
    {
        $this->metadataUri = $metadataUri;

        return $this;
    }

    public function getInsertedAt(): ?\DateTimeImmutable
    {
        return $this->insertedAt;
    }

    public function setInsertedAt(\DateTimeImmutable $insertedAt): static
    {
        $this->insertedAt = $insertedAt;

        return $this;
    }

    public function getImportedAt(): ?\DateTimeImmutable
    {
        return $this->importedAt;
    }

    public function setImportedAt(\DateTimeImmutable $importedAt): static
    {
        $this->importedAt = $importedAt;

        return $this;
    }

    public function getNft(): ?NFT
    {
        return $this->nft;
    }

    public function setNft(NFT $nft): static
    {
        $this->nft = $nft;

        return $this;
    }
}
