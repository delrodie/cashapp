<?php

namespace App\Controller\Api;

use App\Entity\Main\Achat;
use App\Entity\Main\Fournisseur;
use App\Repository\Main\FournisseurRepository;
use App\Repository\Main\ProduitRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/achat')]
class ApiAchatController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository, private SerializerInterface $serializer,
        private FournisseurRepository $fournisseurRepository, private Utilities $utilities,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/', name: 'app_api_achat_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $query = $request->query->get('query'); // Recuperation du terme de recherche

        $produits = $this->produitRepository->findByQuery($query); // Recherche des produits en fonction du terme de recherche

        // Création de tableau de suggestion au format Json
        $suggestions = [];
        foreach ($produits as $produit){
            $suggestions[] = [
                'produitId' => $produit->getId(),
                'libelle' => $produit->getLibelle(),
                'prixVente' => $produit->getPrixVente(),
                'code' => $produit->getReference(),
                'codebarre' => $produit->getCodebarre()
            ];
        }

        return new JsonResponse($suggestions);
    }

    #[Route('/create', name: 'app_api_achat_create', methods: ['POST'])]
    public function create(Request $request)
    {
        $jsonContent = $request->getContent();
        $achatData = json_decode($jsonContent, true);

        // Recuperation du fournisseur
        $fournisseur = $this->fournisseurRepository->findOneBy(['id' => $achatData['fournisseur']]);
        $produits = $achatData['produits'];

        // Enregistrement du nouvel achat
        $achat = new Achat();
        $achat->setCode($this->utilities->codeAchat());
        $achat->setNumFacture($achatData['numFacture']);
        $achat->setDateAchat(new \DateTime($achatData['dateAchat']));
        $achat->setMontant((int) $achatData['montant']);
        $achat->setBenefice((int) $achatData['benefice']);
        $achat->setProduits($produits);
        $achat->setFournisseur($fournisseur);

        $this->entityManager->persist($achat);

        // Mise à jour des produits concernés
        foreach ($produits as $produit){
            $prixAchatUnitaire =(int) ceil($produit["montant"] / $produit['quantite']);
            $entity = $this->produitRepository->findOneBy(['id'=>$produit['id']]);

            if (!$entity) return new JsonResponse(null, Response::HTTP_BAD_REQUEST);

            $entity->setOldPrixAchat($entity->getPrixAchat());
            $entity->setPrixAchat($prixAchatUnitaire);
            $entity->setStock((int)$entity->getStock() + (int) $produit['quantite']);

            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        notyf()->addSuccess("L'achat a été ajouté avec succès!");

        $jsonAchat = json_encode($achat);

        return new JsonResponse($jsonAchat, Response::HTTP_CREATED,[], true);
    }
}