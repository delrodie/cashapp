<?php

namespace App\Controller\Main;

use App\Repository\Main\FactureRepository;
use App\Repository\Main\SynchroRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gestion')]
class GestionController extends AbstractController
{
    #[Route('/', name: 'app_main_gestion_synchro')]
    public function synchro(SynchroRepository $synchroRepository)
    {
        return $this->render('main/gestion/synchro.html.twig',[
            'synchros' => $synchroRepository->findAll()
        ]);
    }

    #[ROute('/facture', name: 'app_main_gestion_facture')]
    public function facture(FactureRepository $factureRepository)
    {
        return $this->render('main/gestion/facture.html.twig',[
            'factures' => $factureRepository->findBy(['sync' => null])
        ]);
    }
}