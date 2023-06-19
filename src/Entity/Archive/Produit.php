<?php

namespace App\Entity\Archive;

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


}
