<?php

namespace App\Entity;

use App\Architecture\EContractType;
use App\Repository\ContractRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?EContractType $type = null;

    #[ORM\Column]
    private array $code = [];

    #[ORM\Column(length: 255, unique: true)]
    private ?string $identifier = null;

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

    public function getType(): ?EContractType
    {
        return $this->type;
    }

    public function setType(EContractType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCode(): array
    {
        return $this->code;
    }

    public function getCodeAsText(): string
    {
        return json_encode($this->code);
    }

    public function setCode(array $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getAbi()
    {
        return $this->code['abi'];
    }

}
