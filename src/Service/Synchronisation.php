<?php

namespace App\Service;

use App\Entity\Main\Client;
use App\Entity\Main\Facture;
use App\Entity\Main\User;
use App\Repository\Main\ClientRepository;
use App\Repository\Main\FactureRepository;
use App\Repository\Main\ProduitRepository;
use App\Repository\Main\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class Synchronisation
{
    public function __construct(
        private EntityManagerInterface $entityManager, private ClientRepository $clientRepository,
        private UserRepository $userRepository, private FactureRepository $factureRepository,
        private ProduitRepository $produitRepository
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
            $newCaisse->setRoles($caisse['password']);
            $newCaisse->setRoles($caisse['roles']);
            $newCaisse->setConnexion($caisse['connexion']);
            $newCaisse->setLastConnectedAt($caisse['lastConnectedAt']);

            $this->userRepository->save($newCaisse, true);
            $entity = $newCaisse;
        }

        return $entity;
    }

    public function facture(array $facture)
    {
        $exist = $this->factureRepository->findOneBy(['code' => $facture['code']]);
        if ($exist) return;

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
            $entity = $this->produitRepository->findOneBy(['reference' => $produit['code']]);
            if (!$entity) return;

            $entity->setStock((int) $entity->getStock() - (int)$produit['quantite']);
            $this->entityManager->persist($entity);
        }

        $this->factureRepository->save($newFacture, true);
        $this->entityManager->flush();

        return true;
    }
}