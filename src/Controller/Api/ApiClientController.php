<?php

namespace App\Controller\Api;

use App\Repository\Main\ClientRepository;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/client')]
class ApiClientController extends AbstractController
{
    public function __construct(private ClientRepository $clientRepository)
    {
    }

    #[Route('/', name:'app_api_client_show',methods: ['GET'])]
    public function show(Request $request)
    {
        $query = $request->query->get('numero'); //dd($query);

        $client = $this->clientRepository->findOneBy(['contact' => $query]);

        $factures = [];
        if ($client){
            $plus=0; $moin=0;
            foreach ($client->getFactures() as $facture){
                $montant = $facture->getMontant();
                if ($montant > 9975){
                    ++$plus;
                }else{
                    ++$moin;
                }
            }

            $factures[] =[
                'nombre' => count($client->getFactures()),
                'plus' => $plus,
                'moins' => $moin
            ];
        }

        return new JsonResponse($factures);
    }
}