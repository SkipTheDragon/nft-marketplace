<?php

namespace App\Entity;

use App\Repository\RpcProviderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RpcProviderRepository::class)]
class RpcProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?array $headers = null;

    #[ORM\ManyToOne(inversedBy: 'rpcProviders')]
    private ?Blockchain $blockchain = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    public function setHeaders(?array $headers): static
    {
        $this->headers = $headers;

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

    public function __toString(): string
    {
        return $this->url;
    }
}
