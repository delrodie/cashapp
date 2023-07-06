<?php

namespace App\Controller\Api;

use App\Entity\Main\Client;
use App\Entity\Main\Facture;
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
    public function __construct(
        private FactureRepository $factureRepository, private Synchronisation $synchronisation,
        private SerializerInterface $serializer,
    )
    {
    }

    #[Route('/', name: 'app_api_synchronisation_create',methods: ['POST'])]
    public function create(Request $request)
    {
//        $factures = $this->factureRepository->getFactureNoSync();
//        $jsonContent = json_encode($factures);

        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true); //dd($data['factures']);

        $message=null;

        foreach ($data['factures'] as $factureData){
            $facture = $this->synchronisation->facture($factureData);
            if ($facture) $message= true;

            return new JsonResponse($message, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}