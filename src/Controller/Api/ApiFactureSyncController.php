<?php

namespace App\Controller\Api;

use App\Repository\Main\FactureRepository;
use App\Service\Synchronisation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/sync-facture')]
class ApiFactureSyncController extends AbstractController
{
    public const FACTURE_EXIST = 101;
    public const PRODUIT_NOT_EXIST = 102;

    public function __construct(private Synchronisation $synchronisation)
    {
    }

    #[Route('/', name: 'api_sync_facture_index',methods: ['POST'])]
    public function index(Request $request)
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true); //dd($jsonContent);

        // Traitement des factures transmises
        if ($data['facture']) {
            foreach ($data['facture'] as $factureData) {
                $facture = $this->synchronisation->facture($factureData); //dd($facture);

                if ($facture === 2) {
                    $message = [
                        'code' => $factureData['code'],
                        'statut' => Self::FACTURE_EXIST
                    ];
                    return new JsonResponse($message, Response::HTTP_OK);
                }

                if ($facture === 3) {
                    $message =[
                        'statut' => self::PRODUIT_NOT_EXIST,
                        'code' => $factureData['code'],
                    ];
                    return new JsonResponse($message, Response::HTTP_OK);
                }

                $message = true;
            }
        }

        return new JsonResponse($message, Response::HTTP_CREATED);
    }
}