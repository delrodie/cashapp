<?php

namespace App\Entity\Archive;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Facture
 *
 * @ORM\Table(name="facture", indexes={@ORM\Index(name="IDX_FE866410A76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class Facture
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reference", type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * @var string|null
     *
     * @ORM\Column(name="date", type="string", length=255, nullable=true)
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="montant", type="integer", nullable=true)
     */
    private $montant;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nombreProduit", type="integer", nullable=true)
     */
    private $nombreproduit;

    /**
     * @var int|null
     *
     * @ORM\Column(name="verse", type="integer", nullable=true)
     */
    private $verse;

    /**
     * @var int|null
     *
     * @ORM\Column(name="monnaie", type="integer", nullable=true)
     */
    private $monnaie;

    /**
     * @var int|null
     *
     * @ORM\Column(name="reduction", type="integer", nullable=true)
     */
    private $reduction;

    /**
     * @var string|null
     *
     * @ORM\Column(name="promotion", type="string", length=255, nullable=true)
     */
    private $promotion;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="statut", type="boolean", nullable=true)
     */
    private $statut;

    /**
     * @var string|null
     *
     * @ORM\Column(name="publie_par", type="string", length=25, nullable=true)
     */
    private $publiePar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="modifie_par", type="string", length=25, nullable=true)
     */
    private $modifiePar;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="publie_le", type="datetime", nullable=true)
     */
    private $publieLe;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="modifie_le", type="datetime", nullable=true)
     */
    private $modifieLe;

    /**
     * @var \FosUser
     *
     * @ORM\ManyToOne(targetEntity="FosUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): static
    {
        $this->date = $date;

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

    public function getNombreproduit(): ?int
    {
        return $this->nombreproduit;
    }

    public function setNombreproduit(?int $nombreproduit): static
    {
        $this->nombreproduit = $nombreproduit;

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

    public function getReduction(): ?int
    {
        return $this->reduction;
    }

    public function setReduction(?int $reduction): static
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getPromotion(): ?string
    {
        return $this->promotion;
    }

    public function setPromotion(?string $promotion): static
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getPubliePar(): ?string
    {
        return $this->publiePar;
    }

    public function setPubliePar(?string $publiePar): static
    {
        $this->publiePar = $publiePar;

        return $this;
    }

    public function getModifiePar(): ?string
    {
        return $this->modifiePar;
    }

    public function setModifiePar(?string $modifiePar): static
    {
        $this->modifiePar = $modifiePar;

        return $this;
    }

    public function getPublieLe(): ?\DateTimeInterface
    {
        return $this->publieLe;
    }

    public function setPublieLe(?\DateTimeInterface $publieLe): static
    {
        $this->publieLe = $publieLe;

        return $this;
    }

    public function getModifieLe(): ?\DateTimeInterface
    {
        return $this->modifieLe;
    }

    public function setModifieLe(?\DateTimeInterface $modifieLe): static
    {
        $this->modifieLe = $modifieLe;

        return $this;
    }

    public function getUser(): ?FosUser
    {
        return $this->user;
    }

    public function setUser(?FosUser $user): static
    {
        $this->user = $user;

        return $this;
    }


}
