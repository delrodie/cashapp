<?php

namespace App\Entity\Main;

use App\Repository\Main\DestockageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DestockageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Destockage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $produits = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sync = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
