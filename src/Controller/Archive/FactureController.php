<?php

namespace App\Controller\Archive;

use App\Repository\Archive\ArchiveFactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/archive/facture')]
#[isGranted('ROLE_GERANT')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_archive_facture_index')]
    public function index(ArchiveFactureRepository $archiveFactureRepository): Response
    {
//        $factures = $archiveFactureRepository->getRecetteJournaliereByCaisse(); dd($factures);
//        dd($archiveFactureRepository->getAll());
        return $this->render('archive/facture/index.html.twig',[
            'factures' => $archiveFactureRepository->getRecetteJournaliereGlobale()
        ]);
    }

    #[Route('/caisse', name: 'app_archive_facture_caisse')]
    public function caisse(ArchiveFactureRepository $archiveFactureRepository)
    {
        return $this->render('archive/facture/caisse.html.twig',[
            'factures' => $archiveFactureRepository->getRecetteJournaliereByCaisse()
        ]);
    }
}