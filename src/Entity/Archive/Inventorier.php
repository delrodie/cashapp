<?php

namespace App\Entity\Archive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inventorier
 *
 * @ORM\Table(name="inventorier", indexes={@ORM\Index(name="IDX_E46BB6ABCE430A85", columns={"inventaire_id"}), @ORM\Index(name="IDX_E46BB6ABF347EFB", columns={"produit_id"})})
 * @ORM\Entity
 */
class Inventorier
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
     * @ORM\Column(name="quantite", type="integer", nullable=true)
     */
    private $quantite;

    /**
     * @var int|null
     *
     * @ORM\Column(name="montant", type="integer", nullable=true)
     */
    private $montant;

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
     * @var \Inventaire
     *
     * @ORM\ManyToOne(targetEntity="Inventaire")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventaire_id", referencedColumnName="id")
     * })
     */
    private $inventaire;

    /**
     * @var \Produit
     *
     * @ORM\ManyToOne(targetEntity="Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="produit_id", referencedColumnName="id")
     * })
     */
    private $produit;


}
