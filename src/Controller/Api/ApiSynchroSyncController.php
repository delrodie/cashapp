<?php

namespace App\Controller\Api;

use App\Service\Synchronisation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/sync-synchro')]
class ApiSynchroSyncController extends AbstractController
{
    public const SYNCHRO_EXIST = 101;
    public const ENTITE_NOT_EXIST = 102;

    public function __construct(private Synchronisation $synchronisation)
    {
    }

    #[ROute('/', name: 'app_api_synchro_index', methods: ['POST'])]
    public function index(Request $request)
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true); //dd($jsonContent);

        // Traitement des achats transmis
        if ($data['synchro']) { //var_dump($data);
            foreach ($data['synchro'] as $synchroData) { //dd($synchroData);
                $synchro = $this->synchronisation->synchro($synchroData);
                if ($synchro === 2) {
                    $message = [
                        'code' => $synchroData['code'],
                        'statut' => self::SYNCHRO_EXIST
                    ];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                if ($synchro === 3) {
                    $message = [
                        'statut' => self::ENTITE_NOT_EXIST,
                        'code' => $synchroData['code']
                    ];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                $message = true;

            }
        }

        return new JsonResponse($message, Response::HTTP_CREATED);
    }
}