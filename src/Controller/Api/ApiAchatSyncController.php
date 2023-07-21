<?php

namespace App\Controller\Api;

use App\Service\Synchronisation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/sync-achat')]
class ApiAchatSyncController extends AbstractController
{
    public const ACHAT_EXIST = 101;
    public const CATEGORIE_NOT_EXIST = 102;

    public function __construct(private Synchronisation $synchronisation)
    {
    }

    #[Route('/', name: 'api_sync_achat_index',methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true); //dd($jsonContent);

        // Traitement des achats transmis
        if ($data['achat']) { //var_dump($data);
            foreach ($data['achat'] as $achatData) { //dd($achatData);
                $achat = $this->synchronisation->achat($achatData);
                if ($achat === 2) {
                    $message = [
                        'code' => $achatData['code'],
                        'statut' => self::ACHAT_EXIST
                    ];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                if ($achat === 3) {
                    $message = ['statut' => self::CATEGORIE_NOT_EXIST];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                $message = true;

            }
        }

        return new JsonResponse($message, Response::HTTP_CREATED);

    }

}