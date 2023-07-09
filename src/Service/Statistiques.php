<?php

namespace App\Service;

use App\Entity\Archive\Facture;
use App\Repository\Archive\ArchiveFactureRepository;
use App\Repository\Main\ClientRepository;
use App\Repository\Main\FactureRepository;
use App\Repository\Main\ProduitRepository;
use App\Repository\Main\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class Statistiques
{
    public function __construct(
        private ProduitRepository $produitRepository, private UserRepository $userRepository,
        private ClientRepository $clientRepository, private FactureRepository $factureRepository,
        private ArchiveFactureRepository $archiveFactureRepository, private EntityManagerInterface $entityManager,
    )
    {
    }

    public function valeurStock(): array
    {
        $produits = $this->produitRepository->findAll();
        $vente=0; $achat=0;
        foreach ($produits as $produit){
            $stock = (int) $produit->getStock();
            if ($stock < 0) $stock =0;

            $pvTotal = (int) $produit->getPrixVente() * $stock;
            $paTotal = (int) $produit->getPrixAchat() * $stock;

            $vente += $pvTotal;
            $achat += $paTotal;
        }

        return [
            'vente' => $vente,
            'achat' => $achat,
            'benefice' => $vente - $achat,
        ];
    }

    public function recetteCaisse(string $debut=null, string $fin = null): array
    {
        $users = $this->userRepository->findAll();

        $ventes=[];
        foreach ($users as $user){
            $exist = $this->factureRepository->getVenteByCaisse($user->getId(), $debut, $fin);
            if ($exist) {
                $ventes[] = [
                    'id' => $user->getId(),
                    'username' => $user->getUserIdentifier(),
                    'montant' => $exist[0][1]
                ];
            }
        }

        return $ventes;
    }

    public function dernieresFactures(int $limit=null): array
    {
        $listes =  $this->factureRepository->getListDesc($limit);
        $factures=[];
        foreach ($listes as $liste){
            $factures[] = [
                'id' => $liste->getId(),
                'montant' => $liste->getNap(),
                'reference' => $liste->getCode(),
                'date' => $liste->getCreatedAt(),
                'client' => $liste->getClient()->getContact(),
                'caisse' => $liste->getCaisse()->getUsername()
            ];
        }

        return $factures;
    }

    public function topClient(array $periode=null): array
    {
        $listes = $this->clientRepository->findAll();
        $clients=[];
        foreach ($listes as $liste){
            // Calcul de la somme des factures
            $factures = $liste->getFactures()->toArray();
            $montant = array_reduce($factures, function ($carry, $facture){
                return $carry + $facture->getNap();
            }, 0);

            $clients[]=[
                'contact' => $liste->getContact(),
                'code' => $liste->getCode(),
                'montant' => $montant,
                'nombre' => count($liste->getFactures())
            ];
        }

        // Tri du tableau par ordre décroissant
        usort($clients, function ($a, $b){
            return $b['montant'] <=> $a['montant'];
        });

        return $clients;
    }

    public function recetteTotalParMois(int $annee=null)
    {
        if (!$annee) $annee = date('Y');

        $facture=[];
        for ($i = 1; $i <= 12; $i++) {
            if ($i <=9 ) $i = "0{$i}";
            $debut = "{$annee}-{$i}-01 00:00:00";
            $timestamp = strtotime($debut);
            $fin = date('Y-m-t 23:59:59', $timestamp);

            $ventes = $this->factureRepository->getVenteByPeriode($debut, $fin);
            $montant=0;
            foreach ($ventes as $vente){
                if ($vente->getNap())
                    $montant += (int) $vente->getNap();
            }

            $recette = $this->recetteCaisse($debut, $fin);

            $facture[] = [
                'mois' => "{$i}/01/{$annee}",
                'montant' => $montant,
                'caisse' => $recette
            ];
        }
//        dd($facture);
        return $facture;
    }

    public function recetteJournaliere()
    {
        $factures = $this->factureRepository->findBy([],['id'=>"DESC"]);

        $napSumByDate = [];
        foreach ($factures as $facture) {
            $createdAt = $facture->getCreatedAt();
            $formattedCreatedAt = $createdAt->format('Y-d-m');

            $nap = $facture->getNap();

            if (!isset($napSumByDate[$formattedCreatedAt])) {
                $napSumByDate[$formattedCreatedAt] = 0;
            }

            $napSumByDate[$formattedCreatedAt] += $nap;
        }

        $nouveaux=[];
        foreach ($napSumByDate as $date => $value ){
            $nouveaux[]=[
                'date' => $date,
                'totalMontant' => $value
            ];
        }

        $aSupprimer = $this->archiveFactureRepository->findOneBy(['reference'=>"038058"]);
        if ($aSupprimer){
            $this->archiveFactureRepository->remove($aSupprimer, true);
        }

        $anciens = $this->archiveFactureRepository->getRecetteJournaliereGlobale();

        $recettes = array_merge($nouveaux, $anciens); //dd($recettes);

        return $recettes;
    }
}