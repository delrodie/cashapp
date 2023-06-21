<?php

namespace App\Controller\Api;

use App\Entity\Main\Client;
use App\Entity\Main\Facture;
use App\Repository\Main\ClientRepository;
use App\Repository\Main\FactureRepository;
use App\Repository\Main\ProduitRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/facture')]
class ApiFactureController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository, private ClientRepository $clientRepository,
        private Utilities $utilities, private FactureRepository $factureRepository,
        private EntityManagerInterface $entityManager, private SerializerInterface $serializer
    )
    {
    }

    #[Route('/', name: 'app_api_facture_list',methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $query = $request->query->get('query'); // Récupération du code recherché
//        dd($query);

        $produits = $this->produitRepository->findByCodeOrReference($query); //Recherche du produit

        // Creation de tableau de suggestion au format JSON
        $suggestions=[];
        foreach ($produits as $produit){
            $suggestions[] = [
                'produitId' => $produit->getId(),
                'reference' => $produit->getReference(),
                'codebarre' => $produit->getCodebarre(),
                'libelle' => $produit->getLibelle(),
                'prixVente' => $produit->getPrixVente(),
                'stock' => $produit->getStock()
            ];
        }

        return new JsonResponse($suggestions);
    }

    #[Route('/create', name: "app_api_facture_create", methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        $client = $this->client($data['client']);
        $facture = new Facture();
        $facture->setCode($this->utilities->codeFacture());
        $facture->setMontant($data['nap']);
        $facture->setRemise($data['remise']);
        $facture->setNap($data['nap']);
        $facture->setVerse($data['verse']);
        $facture->setMonnaie($data['monnaie']);
        $facture->setProduits($data['produits']);
        $facture->setCreatedAt(new \DateTime());
        $facture->setClient($client);

        $produits = $data['produits'];
        foreach ($produits as $produit){
            $entity = $this->produitRepository->findOneBy(['reference' => $produit['code']]);
            if (!$entity) return new JsonResponse(null, Response::HTTP_BAD_REQUEST);

            $entity->setStock((int)$entity->getStock() - (int)$produit['quantite']);
            $this->entityManager->persist($entity);
        }

        $this->factureRepository->save($facture, true);
        $this->entityManager->flush();

        notyf()->addSuccess("Facture enregistrée avec succès!");

//        $jsonFacture = $this->serializer->serialize($facture, 'json', ['groups'=>"facture"]);
//        dd($jsonFacture);
        return new JsonResponse($facture->getId(), Response::HTTP_CREATED,[],true);
    }

    protected function client(string $clientData)
    {
        // Si le client n'existe pas, on le crée
        $client = $this->clientRepository->findOneBy(['contact' => $clientData]);
        if (!$client){
            $client = new Client();
            $client->setCode($this->utilities->codeClient());
            $client->setContact($clientData);
            $this->clientRepository->save($client, true);
        }

        return $client;
    }
}