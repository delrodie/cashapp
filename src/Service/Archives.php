<?php

namespace App\Service;

use App\Repository\Archive\ArchiveFactureRepository;
use App\Repository\Archive\ArchiveInventaireRepository;

class Archives
{
    public function __construct(
        private ArchiveFactureRepository $archiveFactureRepository, private ArchiveInventaireRepository $archiveInventaireRepository
    )
    {
    }

    public function bilan(): array
    {
        $res=[];
        for ($i = 2021; $i <= 2023; $i++){
            $bilans = $this->archiveFactureRepository->getBilan($i);
            $achats = $this->archiveInventaireRepository->getBilan($i);
             $montant=0; $depense=0;
            foreach ($bilans as $bilan){
                $montant += (int)$bilan->getMontant();
            }

            foreach ($achats as $achat){
                $depense +=(int)$achat->getMontant();
            }

            $res[]=[
                'an' => $i,
                'montant' => $montant,
                'depense' => $depense
            ];
        }

        return $res;
    }
}