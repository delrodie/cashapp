<?php

namespace App\Service;

use App\Repository\Main\FactureRepository;

class CalculBenefice
{
    public function __construct(
        private FactureRepository $factureRepository,
    ) {
    }
    /**
     * Arrondit un montant au multiple de 5 inférieur (pas de centimes en FCFA).
     * Ex: 5117 -> 5115, 12024 -> 12020, 5115 -> 5115 (déjà un multiple de 5).
     */
    private function arrondirMultipleDe5(int $montant): int
    {
        return (int) (floor($montant / 5) * 5);
    }

    /**
     * Calcule le bénéfice ET la recette (nap) de chaque facture.
     * Le bénéfice est calculé à partir du prixAchat figé dans chaque ligne de produits,
     * puis arrondi au multiple de 5 inférieur.
     * Retourne : [
     *   ['factureId' => 1, 'date' => '2026-08-05', 'recette' => 45000, 'benefice' => 12020],
     *   ...
     * ]
     */
    public function beneficeParFacture(): array
    {
        $factures = $this->factureRepository->findAllForBenefice();

        $resultats = [];
        foreach ($factures as $facture) {
            $beneficeFacture = 0;

            foreach ($facture['produits'] as $ligne) {
                $prixVente = (int) ($ligne['prixVente'] ?? 0);
                $prixAchat = (int) ($ligne['prixAchat'] ?? 0);
                $quantite  = (int) ($ligne['quantite'] ?? 0);

                $beneficeFacture += ($prixVente - $prixAchat) * $quantite;
            }

            $resultats[] = [
                'factureId' => $facture['factureId'],
                'date' => $facture['createdAt']->format('Y-m-d'),
                'recette' => (int) $facture['nap'],
                'benefice' => $this->arrondirMultipleDe5($beneficeFacture),
            ];
        }

        return $resultats;
    }

    /**
     * Regroupe un résultat de beneficeParFacture() par jour.
     * Retourne : [
     *   '2026-08-05' => ['recette' => 150000, 'benefice' => 45000],
     *   '2026-08-04' => ['recette' => 98000, 'benefice' => 32000],
     *   ...
     * ]
     */
    public function beneficeParJour(array $beneficeParFacture): array
    {
        $parJour = [];
        foreach ($beneficeParFacture as $facture) {
            $jour = $facture['date']; // déjà au format Y-m-d

            if (!isset($parJour[$jour])) {
                $parJour[$jour] = ['recette' => 0, 'benefice' => 0];
            }

            $parJour[$jour]['recette']  += $facture['recette'];
            $parJour[$jour]['benefice'] += $facture['benefice'];
        }

        krsort($parJour); // du plus récent au plus ancien

        return $parJour;
    }

    /**
     * Regroupe un résultat de beneficeParFacture() par mois.
     * Retourne : ['2026-08' => ['recette' => ..., 'benefice' => ...], ...]
     */
    public function beneficeParMois(array $beneficeParFacture): array
    {
        $parMois = [];
        foreach ($beneficeParFacture as $facture) {
            $mois = substr($facture['date'], 0, 7); // 'Y-m-d' -> 'Y-m'

            if (!isset($parMois[$mois])) {
                $parMois[$mois] = ['recette' => 0, 'benefice' => 0];
            }

            $parMois[$mois]['recette']  += $facture['recette'];
            $parMois[$mois]['benefice'] += $facture['benefice'];
        }

        krsort($parMois);

        return $parMois;
    }

    /**
     * Regroupe un résultat de beneficeParFacture() par année.
     * Retourne : ['2026' => ['recette' => ..., 'benefice' => ...], ...]
     */
    public function beneficeParAn(array $beneficeParFacture): array
    {
        $parAn = [];
        foreach ($beneficeParFacture as $facture) {
            $an = substr($facture['date'], 0, 4); // 'Y-m-d' -> 'Y'

            if (!isset($parAn[$an])) {
                $parAn[$an] = ['recette' => 0, 'benefice' => 0];
            }

            $parAn[$an]['recette']  += $facture['recette'];
            $parAn[$an]['benefice'] += $facture['benefice'];
        }

        krsort($parAn);

        return $parAn;
    }
}