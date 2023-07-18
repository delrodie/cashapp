<?php

namespace App\Entity\Main;

use App\Repository\Main\FactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('facture')]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups('facture')]
    private ?int $code = null;

    #[ORM\Column(nullable: true)]
    #[Groups('facture')]
    private ?int $montant = null;

    #[ORM\Column(nullable: true)]
    #[Groups('facture')]
    private ?int $remise = null;

    #[ORM\Column(nullable: true)]
    #[Groups('facture')]
    private ?int $nap = null;

    #[ORM\Column(nullable: true)]
    #[Groups('facture')]
    private ?int $verse = null;

    #[ORM\Column(nullable: true)]
    #[Groups('facture')]
    private ?int $monnaie = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups('facture')]
    private array $produits = [];

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[Groups('facture')]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('facture')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[Groups('facture')]
    private ?User $caisse = null;

    #[ORM\Column(nullable: true)]
    #[Groups('facture')]
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

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(?int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getRemise(): ?int
    {
        return $this->remise;
    }

    public function setRemise(?int $remise): static
    {
        $this->remise = $remise;

        return $this;
    }

    public function getNap(): ?int
    {
        return $this->nap;
    }

    public function setNap(?int $nap): static
    {
        $this->nap = $nap;

        return $this;
    }

    public function getVerse(): ?int
    {
        return $this->verse;
    }

    public function setVerse(?int $verse): static
    {
        $this->verse = $verse;

        return $this;
    }

    public function getMonnaie(): ?int
    {
        return $this->monnaie;
    }

    public function setMonnaie(?int $monnaie): static
    {
        $this->monnaie = $monnaie;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

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

    public function getCaisse(): ?User
    {
        return $this->caisse;
    }

    public function setCaisse(?User $caisse): static
    {
        $this->caisse = $caisse;

        return $this;
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'montant' => $this->montant,
            'remise' => $this->remise,
            'nap' => $this->nap,
            'verse' => $this->verse,
            'monnaie' => $this->monnaie,
            'produits' => $this->produits,
            'client' => $this->client?->jsonSerialize(),
            'createdAt' => $this->createdAt,
            'caisse' => $this->caisse?->jsonSerialize(),
            'sync' => $this->sync,
        ];
    }
}
