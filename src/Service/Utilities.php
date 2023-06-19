<?php

namespace App\Service;

use App\Repository\Main\DomaineRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utilities
{
    public function __construct(
        private DomaineRepository $domaineRepository
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
     * Formattage slug
     * @param string $string
     * @return \Symfony\Component\String\AbstractUnicodeString
     */
    public function slug(string $string)
    {
        return (new AsciiSlugger())->slug(strtolower($string));
    }
}