<?php

namespace App\Service;

use App\Repository\Main\FactureRepository;
use App\Repository\Main\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

class Gestion
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FactureRepository $factureRepository,
        private ProduitRepository $produitRepository
    )
    {
    }

    public function supFacture($facture): void
    {
        $produits = $facture->getProduits() ?? [];
        if ($produits){
            foreach ($produits as $produit){
                $deleteProduit = $this->produitRepository->findOneBy(['reference' => $produit['code']]);
                if ($deleteProduit){
                    $deleteProduit->setStock((int)$deleteProduit->getStock() + (int)$produit['quantite']);
                    $this->entityManager->persist($deleteProduit);
                }
            }
        }

        // Ajout de la facture à la table synchronisation
        $this->factureRepository->remove($facture, true);
        $this->entityManager->flush();
    }
}