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
        private CloudRepository $cloudRepository,
        private Utilities $utilities
    )
    {
    }

    public function supFacture($facture): bool
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
            $contenu = [
                'code' => $facture->getCode(),
                'montant' => $facture->getMontant(),
                'remise' => $facture->getRemise(),
                'nap' => $facture->getNap(),
                'verse' => $facture->getVerse(),
                'monnaie' => $facture->getMonnaie(),
                'produits' => $facture->getProduits(),
                'createdAt' => $facture->getCreatedAt()
            ];
            $this->ajoutSynchro($facture, self::SUPPRESSION, 'FACTURE', $contenu);
        }
        dd('retour');
        $this->factureRepository->remove($facture, true);
        $this->entityManager->flush();

        return true;
    }

    public function ajoutSynchro(object $data, string $action, string $entite, array $contenu): bool
    {
        $cloud = $this->cloudRepository->findOneBy([],['id'=>"DESC"]);
        if ($cloud->getUrl()){
            $synchro = new Synchro();
            $synchro->setCode($this->utilities->codeSynchro());
            $synchro->setAction($action);
            $synchro->setEntite($entite);
            $synchro->setReference($data->getCode());
            $synchro->setCreatedAt(new \DateTime());
            $synchro->setContent($contenu);

            $this->entityManager->persist($synchro);
        }

        return true;
    }
}