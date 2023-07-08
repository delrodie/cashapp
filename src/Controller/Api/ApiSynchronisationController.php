<?php

namespace App\Controller\Api;

use App\Entity\Main\Client;
use App\Entity\Main\Facture;
use App\Repository\Main\AchatRepository;
use App\Repository\Main\FactureRepository;
use App\Service\Synchronisation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;

#[Route('/api/sync')]
class ApiSynchronisationController extends AbstractController
{
    public const SYNCHRO_OK = 100;
    public const FACTURE_EXIST = 101;
    public const PRODUIT_NOT_EXIST = 102;

    public function __construct(
        private FactureRepository $factureRepository, private Synchronisation $synchronisation,
        private SerializerInterface $serializer, private AchatRepository $achatRepository
    )
    {
    }

    #[Route('/', name: 'app_api_synchronisation_create',methods: ['POST'])]
    public function create(Request $request)
    {
//        $factures = $this->factureRepository->getFactureNoSync();
//        $jsonContent = json_encode($factures); dd($jsonContent);
//        $achats = $this->achatRepository->getAchatNoSync();
//        $jsonContent = json_encode($achats);

        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true); //dd($jsonContent);

        $message=null;

        if ($data['achats']) {
            foreach ($data['achats'] as $achatData) {
                $achat = $this->synchronisation->achat($achatData);
                if ($achat) $message = true;
                else return new JsonResponse(null, Response::HTTP_NO_CONTENT);
            }
        }

        // Traitement des factures transmises
        if ($data['factures']) {
            foreach ($data['factures'] as $factureData) {
                $facture = $this->synchronisation->facture($factureData);

                if ($facture === 2) return new JsonResponse($factureData['code'], self::FACTURE_EXIST);
                elseif ($facture === 3) return new JsonResponse(null, self::PRODUIT_NOT_EXIST);
                else $message = true;
            }
        }

        return new JsonResponse($message, self::SYNCHRO_OK);
    }
}