<?php

declare(strict_types=1);

namespace App\Controller\Main;

use App\Repository\Main\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailsBeneficeController extends AbstractController
{
    public function __construct(private readonly FactureRepository $factureRepository)
    {
    }

    #[Route('/{jour}', name: 'app_main_detailsbenefice_jour', methods: ['GET'])]
    public function jour($jour)
    {
        $factures = $this->factureRepository->findFactureByJour($jour);

        // Calcul du bénéfice par produit et par facture
        foreach ($factures as $facture) {
            $beneficeTotalFacture = 0;
            $produitsModifies = [];

            foreach ($facture->getProduits() as $produit) {
                $prixAchat = $produit['prixAchat'] ?? 0;
                $prixVente = $produit['prixVente'] ?? 0;
                $quantite = $produit['quantite'] ?? 0;

                // Benefice = (Prix Vente - Prix Achat) * Quantité
                $beneficeProduit = ($prixVente - $prixAchat) * $quantite;
                $beneficeTotalFacture += $beneficeProduit;

                $produit['benefice'] = $beneficeProduit;
                $produitsModifies[] = $produit;
            }

            // Propriétés dynamiques pour Twig
            $facture->produitsAvecBenefice = $produitsModifies;
            $facture->beneficeTotal = $beneficeTotalFacture;
        }


        return $this->render('benefice/details_jour.html.twig',[
            'factures' => $factures
        ]);
    }
}
