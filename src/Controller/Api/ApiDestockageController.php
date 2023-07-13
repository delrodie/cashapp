<?php

namespace App\Controller\Api;

use App\Entity\Main\Destockage;
use App\Repository\Main\DestockageRepository;
use App\Repository\Main\ProduitRepository;
use App\Service\Utilities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/destockage')]
class ApiDestockageController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProduitRepository $produitRepository,
        private DestockageRepository $destockageRepository,
        private Utilities $utilities
    )
    {
    }

    #[Route('/', name: 'app_api_destockage_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $query = $request->query->get('query'); // Récupération du terme de recherche

        $suggestions = $this->utilities->produitQuery($query);

        return new JsonResponse($suggestions);
    }

    #[Route('/create', name: 'app_api_destockage_create',methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $jsonContent = $request->getContent();
        $destockageData = json_decode($jsonContent, true);

        $produits = $destockageData['produits'];

        // Enregistrement d'un nouveau destockage
        $destockage = new Destockage();
        $destockage->setMotif($destockageData['motif']);
        $destockage->setMontant($destockageData['montant']);
        $destockage->setProduits($produits);
        $destockage->setCode($this->utilities->codeDestockage());
        $destockage->setUser($this->getUser());

        $this->entityManager->persist($destockage);

        // Mise à jour des produits concernés
        foreach ($produits as $produit){
            $entity = $this->produitRepository->findOneBy(['id' => $produit['id'] ]);

            if (!$entity) {
                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->addError("Le produit {$produit['libelle']} n'a pas été trouvé");
                return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
            }

            $quantite = (int)$produit['quantite'];
            $stock = (int) $entity->getStock();
            $reste = $stock - $quantite;

            if ($reste < 0){
                $message = "Echec, la quantité en stock ({$stock}) du produit ({$entity->getLibelle()}) est inférieure à la quantité ({$quantite}) à destocker!";
                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->addError($message);
                return new JsonResponse($message, Response::HTTP_OK);
            }

            $entity->setStock($reste);

            $this->entityManager->persist($entity);

        }

        $this->entityManager->flush();

        notyf()->addSuccess("Le destockage a été enregistré avec succès!");

        return new JsonResponse($destockage->getId(), Response::HTTP_CREATED, [], true);
    }
}