<?php

namespace App\Controller\Etat;

use App\Repository\Main\FactureRepository;
use App\Repository\Main\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/etat/facture')]
#[isGranted('ROLE_GERANT')]
class FactureController extends AbstractController
{
    public function __construct(private FactureRepository $factureRepository, private UserRepository $userRepository)
    {
    }

    #[Route('/', name: 'app_etat_facture_global')]
    public function global(): Response
    {
//        $factures = $this->factureRepository->findAllList(); dd($factures);
//        dd($this->factureRepository->getTotalNAP());
        return $this->render('etat/facture/index.html.twig', [
            'factures' =>$this->factureRepository->findAllList() ,
            'total' => $this->factureRepository->getTotalNAP()
        ]);
    }

    #[Route('/caisse', name: 'app_etat_facture_caisse')]
    public function caisse(Request $request)
    {
        $debut = $request->get('debut');
        $fin = $request->get('fin');

        $users = $this->userRepository->findAll();
        $caisse=[]; $total=0;
        foreach ($users as $user){
            $factures = $this->factureRepository->findByCaisseAndPeriode($user->getId(), $debut, $fin);
            if ($factures){
                $montant=0;
                foreach ($factures as $facture){
                    $montant += $facture->getNap();
                }

                $caisse[] = [
                    'user' => $user->getUserIdentifier(),
                    'montant' => $montant,
                    'facture' => count($factures)
                ];

                $total+=$montant;
            }
        }
//        dd($caisse);
        return $this->render('etat/facture/caisse.html.twig',[
            'caisses' => $caisse,
            'total' => $total
        ]);
    }
}
