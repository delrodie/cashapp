<?php

namespace App\Service;

use App\Repository\Main\AchatRepository;
use App\Repository\Main\CategorieRepository;
use App\Repository\Main\ClientRepository;
use App\Repository\Main\DestockageRepository;
use App\Repository\Main\DomaineRepository;
use App\Repository\Main\FactureRepository;
use App\Repository\Main\FournisseurRepository;
use App\Repository\Main\ProduitRepository;
use App\Repository\Main\UserRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;

class Utilities
{
    public function __construct(
        private DomaineRepository $domaineRepository, private CategorieRepository $categorieRepository,
        private ProduitRepository $produitRepository, private FournisseurRepository $fournisseurRepository,
        private AchatRepository $achatRepository, private ClientRepository $clientRepository,
        private FactureRepository $factureRepository, private UserRepository $userRepository,
        private DestockageRepository $destockageRepository
    )
    {
    }

    /**
     * Generation de code de l'entité Domaine
     * @return int
     */
    public function codeDomaine(): int
    {
        $lastDomaine = $this->domaineRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastDomaine)  return 10;

        return (int) $lastDomaine->getCode() + 1;
    }

    /**
     * Generation de code de l'entité Categorie
     *
     * @param object $categorie
     * @return int|string|null
     */
    public function codeCategorie(object $categorie, bool $update=false)
    {
        if (!$update) {
            $lastCategorie = $this->categorieRepository->findOneBy(['domaine' => $categorie->getDomaine()->getId()], ['id' => "DESC"]);

            if (!$lastCategorie) return $categorie->getDomaine()->getCode() . '' . 11;

            return $lastCategorie->getCode() + 1;
        }

        return $categorie->getDomaine()->getCode().''. $categorie->getId();
    }

    public function referenceProduit(object $produit, bool $update=false): string
    {
        if (!$update){
            $lastProduit = $this->produitRepository->findOneBy([],['id'=>"DESC"]);
            if (!$lastProduit) return $produit->getCategorie()->getCode().''. 1001;

            return $produit->getCategorie()->getCode().''. $lastProduit->getId() + 1001;
        }

        return $produit->getCategorie()->getCode().''.substr($produit->getReference(), -4);
    }

    /**
     * Generation du code du fournisseur
     *
     * @return string
     */
    public function codeFournisseur(): string
    {
        $lastFournisseur = $this->fournisseurRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastFournisseur) return "F101";

        return "F". $lastFournisseur->getId() + 101;
    }

    public function codeAchat()
    {
        $lastAchat = $this->achatRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastAchat) return 1001;

        return $lastAchat->getId() + 1001;
    }

    public function codeClient()
    {
        $lastClient = $this->clientRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastClient) return 1001;

        return $lastClient->getId() + 1001;
    }

    public function codeFacture()
    {
        $lastFacture = $this->factureRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastFacture) return 100001;

        return $lastFacture->getId() + 100001;
    }

    public function codeDestockage()
    {
        $lastDestockage = $this->destockageRepository->findOneBy([],['id'=>"DESC"]);
        if (!$lastDestockage) return 1001;

        return  $lastDestockage->getId() + 1001;
    }

    public function getUsers(string $username): array
    {
        $getUsers = $this->userRepository->findWithout($username);
        $users = [];
        foreach ($getUsers as $getUser){
            $roles = $getUser->getRoles()[0] ?? $getUser->getRoles();
            switch ($roles) {
                case 'ROLE_ADMIN' :
                    $role = 'Administrateur';
                    break;
                case 'ROLE_GERANT':
                    $role = 'Gérant';
                    break;
                case 'ROLE_CAISSE':
                    $role = 'Caisse';
                    break;
                default:
                    $role = 'Utilisateur';
                    break;
            }
            $users[] = [
                'id' => $getUser->getId(),
                'userIdentifier' => $getUser->getUserIdentifier(),
                'role' => $role,
                'connexion' => $getUser->getConnexion(),
                'lastConnectedAt' => $getUser->getLastConnectedAt()
            ];
        };

        return $users;
    }

    public function produitQuery($query): array
    {
        $produits = $this->produitRepository->findByQuery($query); // Recherche du produit en fonction du terme de recherche

        // Création du tableau de suggestion au format Json
        $suggestions=[];
        foreach ($produits as $produit){
            $suggestions[] = [
                'produitId' => $produit->getId(),
                'libelle' => $produit->getLibelle(),
                'prixVente' => $produit->getPrixVente(),
                'code' => $produit->getReference(),
                'codebarre' => $produit->getCodebarre()
            ];
        }

        return $suggestions;
    }

    /**
     * Formattage slug
     * @param string $string
     * @return \Symfony\Component\String\AbstractUnicodeString
     */
    public function slug(string $string)
    {
        return (new AsciiSlugger())->slug(strtolower($string));
    }
}