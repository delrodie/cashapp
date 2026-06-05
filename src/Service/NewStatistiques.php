<?php

namespace App\Service;

use App\Repository\Archive\ArchiveFactureRepository;
use App\Repository\Main\FactureRepository;
use App\Repository\Main\ProduitRepository;
use App\Repository\Main\UserRepository;

class NewStatistiques
{
    public function __construct(
        private FactureRepository $factureRepository,
        private UserRepository $userRepository,
        private ArchiveFactureRepository $archiveFactureRepository,
        private ProduitRepository $produitRepository
    )
    {
    }


    public function valeurStock(): array
    {
        // 1. On récupère les totaux calculés par la base de données (Ultra rapide)
        $totals = $this->produitRepository->getValeurStockTotals();

        $vente = (int) $totals['vente'];
        $achat = (int) $totals['achat'];

        // 2. On retourne le tableau propre
        return [
            'vente' => $vente,
            'achat' => $achat,
            'benefice' => $vente - $achat,
        ];
    }


    public function recetteCaisse(?string $debut = null, ?string $fin = null): array
    {
        // 1. Récupération des recettes de la base active (1 seule requête SQL globale)
        $ventesActuelles = $this->factureRepository->getRecettesRegroupeesParCaisse($debut, $fin);

        // 2. Récupération des recettes de l'archive
        $ventesAnciennes = $this->archiveFactureRepository->getRecetteByCaisse($debut, $fin) ?? [];

        // 3. Fusion et cumul des montants par ID ou Username de manière professionnelle
        $recetteCumulee = [];

        foreach (array_merge($ventesActuelles, $ventesAnciennes) as $item) {
            $username = $item['username'];
            $montant = (int) $item['montant'];

            if (!isset($recetteCumulee[$username])) {
                $recetteCumulee[$username] = [
                    'id' => (int) $item['id'],
                    'username' => $username,
                    'montant' => $montant,
                ];
            } else {
                $recetteCumulee[$username]['montant'] += $montant;
            }
        }

        // array_values() réindexe automatiquement le tableau de 0 à N sans faire de boucle foreach manuelle
        return array_values($recetteCumulee);
    }

    public function dernieresFactures(?int $limit = null): array
    {
        return $this->factureRepository->getDernieresFacturesTableau($limit);
    }

    public function recetteTotalParMois(?int $annee = null): array
    {
        // Si l'année n'est pas fournie, on prend l'année en cours
        $annee ??= (int)date('Y');

        // 1. On récupère toutes les données de l'année en seulement 2 requêtes SQL
        $totauxMensuels = $this->factureRepository->getTotauxMensuels($annee);
        $caissesMensuelles = $this->factureRepository->getVentesCaisseParMois($annee);

        $facture = [];

        // 2. On formate le résultat final pour les 12 mois
        for ($i = 1; $i <= 12; $i++) {
            $moisFormate = str_pad($i, 2, '0', STR_PAD_LEFT);

            $facture[] = [
                'mois' => "{$moisFormate}/01/{$annee}",
                'montant' => $totauxMensuels[$i],
                'caisse' => $caissesMensuelles[$i]
            ];
        }

        return $facture;
    }
}