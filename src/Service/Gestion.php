<?php

namespace App\Service;

use App\Entity\Main\Synchro;
use App\Repository\Main\CloudRepository;
use App\Repository\Main\FactureRepository;
use App\Repository\Main\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Self_;

class Gestion
{
    public const CREATION = "CREATION";
    public const MODIFICATION = "MODIFICATION";
    public const SUPPRESSION = "SUPPRESSION";

    public function __construct(
        private EntityManagerInterface $entityManager,
        private FactureRepository $factureRepository,
        private ProduitRepository $produitRepository,
        private CloudRepository $cloudRepository
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


        // Ajout de la facture à la table synchronisation si nous sommes en local
        if ($facture->isSync()){
            $this->ajoutSynchro($facture, self::SUPPRESSION, 'facture');
        }

        $this->factureRepository->remove($facture, true);
        $this->entityManager->flush();
    }

    public function ajoutSynchro(object $data, string $action, string $entite): void
    {
        $cloud = $this->cloudRepository->findOneBy([],['id'=>"DESC"]);
        if ($cloud->getUrl()){
            $synchro = new Synchro();
            $synchro->setAction($action);
            $synchro->setEntite($entite);
            $synchro->setReference($data->getCode());
            $synchro->setCreatedAt(new \DateTime());

            $this->entityManager->persist($synchro);
        }
    }
}