<?php

namespace App\Service;

use App\Entity\Main\Achat;
use App\Entity\Main\Client;
use App\Entity\Main\Facture;
use App\Entity\Main\Fournisseur;
use App\Entity\Main\Produit;
use App\Entity\Main\User;
use App\Repository\Main\AchatRepository;
use App\Repository\Main\CategorieRepository;
use App\Repository\Main\ClientRepository;
use App\Repository\Main\FactureRepository;
use App\Repository\Main\FournisseurRepository;
use App\Repository\Main\ProduitRepository;
use App\Repository\Main\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class Synchronisation
{
    public function __construct(
        private EntityManagerInterface $entityManager, private ClientRepository $clientRepository,
        private UserRepository $userRepository, private FactureRepository $factureRepository,
        private ProduitRepository $produitRepository, private AchatRepository $achatRepository,
        private FournisseurRepository $fournisseurRepository, private Utilities $utilities,
        private CategorieRepository $categorieRepository
    )
    {
    }

    public function client(array $client)
    {
        $entity = $this->clientRepository->findOneBy(['contact' => $client['contact']]);
        if (!$entity){
            $newClient = new Client();
            $newClient->setCode($client['code']);
            $newClient->setContact($client['contact']);
            $newClient->setIdentite($client['identite']);

            $this->clientRepository->save($newClient, true);
            $entity = $newClient;
        }

        return $entity;
    }

    public function caisse(array $caisse)
    {
        $entity = $this->userRepository->findOneBy(['username' => $caisse['username']]);
        if (!$entity){
            $newCaisse = new User();
            $newCaisse->setUsername($caisse['username']);
            $newCaisse->setPassword($caisse['password']);
            $newCaisse->setRoles($caisse['roles']);
            $newCaisse->setConnexion($caisse['connexion']);
            $newCaisse->setLastConnectedAt(new \DateTime($caisse['lastConnectedAt']['date']));

            $this->userRepository->save($newCaisse, true);
            $entity = $newCaisse;
        }

        return $entity;
    }

    public function facture(array $facture)
    {
        $exist = $this->factureRepository->findOneBy(['code' => $facture['code']]);
        if ($exist) { dd('entrez de la boucle de condition');
            return;
        }

        $newFacture = new Facture();
        $newFacture->setCode($facture['code']);
        $newFacture->setMontant($facture['montant']);
        $newFacture->setRemise($facture['remise']);
        $newFacture->setNap($facture['nap']);
        $newFacture->setVerse($facture['verse']);
        $newFacture->setMonnaie($facture['monnaie']);
        $newFacture->setProduits($facture['produits']);
        $newFacture->setCreatedAt(new \DateTime($facture['createdAt']['date']));
        $newFacture->setSync(true);
        $newFacture->setClient($this->client($facture['client']));
        $newFacture->setCaisse($this->caisse($facture['caisse']));

        // Mise a jour de la table produit
        foreach ($facture['produits'] as $produit){
            $entity = $this->produitRepository->findOneBy(['reference' => (int) $produit['code']]); dd($entity);
            if (!$entity) return;

            $entity->setStock((int) $entity->getStock() - (int)$produit['quantite']);
            $entity->setPrixVente((int) $produit['prixVente']);
            $this->entityManager->persist($entity);
        }

        $this->factureRepository->save($newFacture, true);
        $this->entityManager->flush();

        return true;
    }

    public function achat(mixed $achatData)
    {
        $exist = $this->achatRepository->findOneBy(['code' => $achatData['code']]);
        if ($exist) return;

        $newAchat = new Achat();
        $newAchat->setCode($achatData['code']);
        $newAchat->setMontant($achatData['montant']);
        $newAchat->setBenefice($achatData['benefice']);
        $newAchat->setNumFacture($achatData['numFacture']);
        $newAchat->setDateAchat(new \DateTime($achatData['dateAchat']['date']));
        $newAchat->setProduits($achatData['produits']);
        $newAchat->setSync(true);
        $newAchat->setFournisseur($this->fournisseur($achatData['fournisseur']));

        // Mise a jour de la table produit
        foreach ($achatData['produits'] as $produit){
            $prixUnitaire = (int) ceil($produit['montant'] / $produit['quantite']);
            $entity = $this->produitRepository->findOneBy(['reference' => $produit['code']] );
            if ($entity){
                $entity->setOldPrixAchat($entity->getPrixAchat());
                $entity->setPrixAchat($prixUnitaire);
                $entity->setStock((int)$entity->getStock() + (int)$produit['quantite']);

                $this->entityManager->persist($entity);
            }else{
                // Recuperation de la categorie concernée
                $code = substr(strval($produit['code']),0,4);
                $categorie = $this->categorieRepository->findOneBy(['code' => $code]);
                if (!$categorie) return;

                // Instanciation du nouveau produit
                $newProduit = new Produit();
                $newProduit->setLibelle($produit['libelle']);
                $newProduit->setStock($produit['quantite']);
                $newProduit->setPrixAchat($prixUnitaire);
                $newProduit->setReference($produit['code']);
                $newProduit->setSlug($this->utilities->slug($produit['libelle']));
                $newProduit->setCodebarre($produit['codebarre']);
                $newProduit->setCategorie($categorie);

                $this->entityManager->persist($newProduit);
            }
        }

        $this->achatRepository->save($newAchat, true);
        $this->entityManager->flush();

        return true;
    }

    public function fournisseur(array $fournisseur): Fournisseur
    {
        $exist = $this->fournisseurRepository->findOneBy(['code' => $fournisseur['code']]);
        if (!$exist){
            $newFseur = new Fournisseur();
            $newFseur->setCode($fournisseur['code']);
            $newFseur->setContact($fournisseur['contact']);
            $newFseur->setNom($fournisseur['nom']);

            $this->fournisseurRepository->save($newFseur, true);

            return $newFseur;
        }

        return $exist;
    }
}