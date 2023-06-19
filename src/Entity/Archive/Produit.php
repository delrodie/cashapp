<?php

namespace App\Entity\Archive;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="produit", indexes={@ORM\Index(name="IDX_29A5EC27BCF5E72D", columns={"categorie_id"})})
 * @ORM\Entity
 */
class Produit
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
     * @ORM\Column(name="codebarre", type="string", length=255, nullable=true)
     */
    private $codebarre;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var int|null
     *
     * @ORM\Column(name="prixAchat", type="integer", nullable=true)
     */
    private $prixachat;

    /**
     * @var int|null
     *
     * @ORM\Column(name="prixVente", type="integer", nullable=true)
     */
    private $prixvente;

    /**
     * @var int|null
     *
     * @ORM\Column(name="old_prixAchat", type="integer", nullable=true)
     */
    private $oldPrixachat;

    /**
     * @var int|null
     *
     * @ORM\Column(name="old_prixVente", type="integer", nullable=true)
     */
    private $oldPrixvente;

    /**
     * @var int|null
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    private $stock;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="vente", type="boolean", nullable=true)
     */
    private $vente;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="inventaire", type="boolean", nullable=true)
     */
    private $inventaire;

    /**
     * @var int|null
     *
     * @ORM\Column(name="seuil", type="integer", nullable=true)
     */
    private $seuil;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=75, nullable=false)
     */
    private $slug;

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
     * @var \Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     */
    private $categorie;

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

    public function getCodebarre(): ?string
    {
        return $this->codebarre;
    }

    public function setCodebarre(?string $codebarre): static
    {
        $this->codebarre = $codebarre;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPrixachat(): ?int
    {
        return $this->prixachat;
    }

    public function setPrixachat(?int $prixachat): static
    {
        $this->prixachat = $prixachat;

        return $this;
    }

    public function getPrixvente(): ?int
    {
        return $this->prixvente;
    }

    public function setPrixvente(?int $prixvente): static
    {
        $this->prixvente = $prixvente;

        return $this;
    }

    public function getOldPrixachat(): ?int
    {
        return $this->oldPrixachat;
    }

    public function setOldPrixachat(?int $oldPrixachat): static
    {
        $this->oldPrixachat = $oldPrixachat;

        return $this;
    }

    public function getOldPrixvente(): ?int
    {
        return $this->oldPrixvente;
    }

    public function setOldPrixvente(?int $oldPrixvente): static
    {
        $this->oldPrixvente = $oldPrixvente;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isVente(): ?bool
    {
        return $this->vente;
    }

    public function setVente(?bool $vente): static
    {
        $this->vente = $vente;

        return $this;
    }

    public function isInventaire(): ?bool
    {
        return $this->inventaire;
    }

    public function setInventaire(?bool $inventaire): static
    {
        $this->inventaire = $inventaire;

        return $this;
    }

    public function getSeuil(): ?int
    {
        return $this->seuil;
    }

    public function setSeuil(?int $seuil): static
    {
        $this->seuil = $seuil;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }


}
