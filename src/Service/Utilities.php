<?php

namespace App\Service;

use App\Repository\Main\CategorieRepository;
use App\Repository\Main\DomaineRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utilities
{
    public function __construct(
        private DomaineRepository $domaineRepository, private CategorieRepository $categorieRepository
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