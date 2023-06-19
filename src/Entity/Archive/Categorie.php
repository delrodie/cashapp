<?php

namespace App\Entity\Archive;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie", indexes={@ORM\Index(name="IDX_497DD6344272FC9F", columns={"domaine_id"})})
 * @ORM\Entity
 */
class Categorie
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
     * @var int|null
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255, nullable=false)
     */
    private $libelle;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nombreProduit", type="integer", nullable=true)
     */
    private $nombreproduit;

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
     * @var \Domaine
     *
     * @ORM\ManyToOne(targetEntity="Domaine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="domaine_id", referencedColumnName="id")
     * })
     */
    private $domaine;

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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    public function getDomaine(): ?Domaine
    {
        return $this->domaine;
    }

    public function setDomaine(?Domaine $domaine): static
    {
        $this->domaine = $domaine;

        return $this;
    }


}
