<?php

namespace App\Controller\Api;

use App\Entity\Main\Inventaire;
use App\Repository\Main\ProduitRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/inventaire')]
class ApiInventaireController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository, private Utilities $utilities,
        private EntityManagerInterface $entityManager, private SerializerInterface $serializer,
    )
    {
    }

    #[Route('/', name: 'app_api_inventaire_produits', methods: ['GET'])]
    public function produits(Request $request): JsonResponse
    {
        $query = $request->query->get('query'); // Récupération du code recherché

        return new JsonResponse($this->utilities->produitQueryBy($query));
    }

    #[Route('/create', name: 'app_api_inventaire_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        $inventaire = new Inventaire();
        $inventaire->setDate($data['date']);
        $inventaire->setProduits($data['produits']);
        $inventaire->setUser($this->getUser());
        $inventaire->setReference($this->utilities->codeInventaire());

        // Mise a jour des stocks des produits
        $produits = $data['produits'];
        $perte = 0; $gain = 0;
        foreach ($produits as $produit) {
            $entity = $this->produitRepository->findOneBy(['reference' => $produit['code']]);
            if (!$entity) return new JsonResponse(null, Response::HTTP_BAD_REQUEST);

            $entity->setStock((int) $produit['quantite']);
            $this->entityManager->persist($entity);

            if ($produit['equart'] > 0)
                $perte += (int) $produit['equart'] * $entity->getPrixVente();
            else
                $gain += (int) $produit['equart'] * $entity->getPrixVente() * (-1);

        }

        // Complements attributs inventaire
        $inventaire->setPerte($perte);
        $inventaire->setGain($gain);

        $this->entityManager->persist($inventaire);
        $this->entityManager->flush();

        return new JsonResponse([
            'statut' => true,
            'date' => $data['date'],
        ], Response::HTTP_CREATED);
    }
}