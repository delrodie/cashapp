<?php

namespace App\Entity\Main;

use App\Repository\Main\AchatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AchatRepository::class)]
class Achat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('achat')]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups('achat')]
    private ?int $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('achat')]
    private ?string $numFacture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('achat')]
    private ?\DateTimeInterface $dateAchat = null;

    #[ORM\Column(nullable: true)]
    #[Groups('achat')]
    private ?int $montant = null;

    #[ORM\Column(nullable: true)]
    #[Groups('achat')]
    private ?int $benefice = null;

    #[ORM\ManyToOne(inversedBy: 'achats')]
    #[Groups('achat')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups('achat')]
    private array $produits = [];

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

    public function getNumFacture(): ?string
    {
        return $this->numFacture;
    }

    public function setNumFacture(?string $numFacture): static
    {
        $this->numFacture = $numFacture;

        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(?\DateTimeInterface $dateAchat): static
    {
        $this->dateAchat = $dateAchat;

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

    public function getBenefice(): ?int
    {
        return $this->benefice;
    }

    public function setBenefice(?int $benefice): static
    {
        $this->benefice = $benefice;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

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
}
