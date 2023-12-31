<?php

namespace App\Controller\Api;

use App\Entity\Main\Client;
use App\Entity\Main\Facture;
use App\Repository\Main\AchatRepository;
use App\Repository\Main\FactureRepository;
use App\Service\Synchronisation;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Self_;
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
    public const DESTOCKAGE_EXIST = 105;
    public const DESTOCKANGE_NOT_EXIST = 106;

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
        $data = json_decode($jsonContent, true); dd($jsonContent);

        if ($data === null) json_last_error_msg();

        $message=null;

        // Traitement des destockages transmis
        if ($data['destockages']){ //dd($data['destockages']);
            foreach ($data['destockages'] as $destockageData){
                $destockage = $this->synchronisation->destockage($destockageData); //dd($destockage);

                if ($destockage === 2) {
                    $message = [
                        'code' => $destockageData['code'],
                        'statut' => self::DESTOCKAGE_EXIST
                    ];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                if ($destockage === 3) {
                    $message = ['statut' => self::DESTOCKANGE_NOT_EXIST];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                $message = true;
            }
        }

        // Traitement des destockages transmis
        if ($data['destockages']){ //dd($data['destockages']);
            foreach ($data['destockages'] as $destockageData){
                $destockage = $this->synchronisation->destockage($destockageData); //dd($destockage);

                if ($destockage === 2) {
                    $message = [
                        'code' => $destockageData['code'],
                        'statut' => self::DESTOCKAGE_EXIST
                    ];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                if ($destockage === 3) {
                    $message = ['statut' => self::DESTOCKANGE_NOT_EXIST];

                    return new JsonResponse($message, Response::HTTP_OK);
                }

                $message = true;
            }
        }

        return new JsonResponse($message, Response::HTTP_CREATED);
    }

}