<?php

namespace App\Service;

use App\Repository\Main\AchatRepository;
use App\Repository\Main\CategorieRepository;
use App\Repository\Main\DomaineRepository;
use App\Repository\Main\FournisseurRepository;
use App\Repository\Main\ProduitRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utilities
{
    public function __construct(
        private DomaineRepository $domaineRepository, private CategorieRepository $categorieRepository,
        private ProduitRepository $produitRepository, private FournisseurRepository $fournisseurRepository,
        private AchatRepository $achatRepository
    )
    {
    }

    /**
     * Generation de code de l'entité Domaine
     * @return int
     */
    public function codeDomaine(): int
    {
        $lastDomaine = $this->domaineRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastDomaine)  return 10;

        return (int) $lastDomaine->getCode() + 1;
    }

    /**
     * Generation de code de l'entité Categorie
     *
     * @param object $categorie
     * @return int|string|null
     */
    public function codeCategorie(object $categorie, bool $update=false)
    {
        if (!$update) {
            $lastCategorie = $this->categorieRepository->findOneBy(['domaine' => $categorie->getDomaine()->getId()], ['id' => "DESC"]);

            if (!$lastCategorie) return $categorie->getDomaine()->getCode() . '' . 11;

            return $lastCategorie->getCode() + 1;
        }

        return $categorie->getDomaine()->getCode().''. $categorie->getId();
    }

    public function referenceProduit(object $produit, bool $update=false): string
    {
        if (!$update){
            $lastProduit = $this->produitRepository->findOneBy([],['id'=>"DESC"]);
            if (!$lastProduit) return $produit->getCategorie()->getCode().''. 1001;

            return $produit->getCategorie()->getCode().''. $lastProduit->getId() + 1001;
        }

        return $produit->getCategorie()->getCode().''.substr($produit->getReference(), -4);
    }

    /**
     * Generation du code du fournisseur
     *
     * @return string
     */
    public function codeFournisseur(): string
    {
        $lastFournisseur = $this->fournisseurRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastFournisseur) return "F101";

        return "F". $lastFournisseur->getId() + 101;
    }

    public function codeAchat()
    {
        $lastAchat = $this->achatRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastAchat) return 1001;

        return $lastAchat->getId() + 1001;
    }

    /**
     * Formattage slug
     * @param string $string
     * @return \Symfony\Component\String\AbstractUnicodeString
     */
    public function slug(string $string)
    {
        return (new AsciiSlugger())->slug(strtolower($string));
    }
}