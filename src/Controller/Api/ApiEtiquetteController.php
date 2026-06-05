<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\Main\ProduitRepository;
use App\Service\LabelPrinterService;
use App\Service\ZplService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/etiquette')]
class ApiEtiquetteController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ZplService $zplService,
        private ProduitRepository $produitRepository,
    )
    {
    }

    #[Route('/', name: 'api_etiquette_show', methods: ['GET'])]
    public function show($id)
    {
        $produit = $this->produitRepository->findOneBy(['id' => $id]);

        if (!$produit) {
            return $this->json(['error' => 'Produit introuvable'], 404);
        }

        return $this->json([
            'zpl'       => $this->zplService->genererEtiquette($produit),
            'produit'   => $produit->getLibelle(),
            'reference' => $produit->getReference(),
        ]);
    }

    #[Route('/print/zpl', name: 'api_get_zpl', methods: ['POST'])]
    public function getZpl(Request $request, LabelPrinterService $printer): JsonResponse
    {
        $data = $request->toArray(); //dump($data);
        return new JsonResponse(['zpl' => $printer->getZPL($data)]);
    }
}
