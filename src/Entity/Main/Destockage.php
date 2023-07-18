<?php

namespace App\Entity\Main;

use App\Repository\Main\DestockageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DestockageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Destockage implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('destockage')]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups('destockage')]
    private ?int $code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups('destockage')]
    private ?string $motif = null;

    #[ORM\Column(nullable: true)]
    #[Groups('destockage')]
    private ?int $montant = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups('destockage')]
    private array $produits = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('destockage')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne]
    #[Groups('destockage')]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sync = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getProduits(): array
    {
        return $this->produits;
    }

    public function setProduits(?array $produits): static
    {
        $this->produits = $produits;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): \DateTime
    {
        return $this->createdAt = new \DateTime();
    }

    public function isSync(): ?bool
    {
        return $this->sync;
    }

    public function setSync(?bool $sync): static
    {
        $this->sync = $sync;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'createdAt' => $this->createdAt,
            'montant' => $this->montant,
            'produits' => $this->produits,
            'user' => $this->user?->jsonSerialize()
        ];
    }
}
