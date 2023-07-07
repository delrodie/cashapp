<?php

namespace App\Controller\Archive;

use App\Repository\Archive\ArchiveFactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/archive/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_archive_facture_index')]
    public function index(ArchiveFactureRepository $archiveFactureRepository)
    {
//        dd($archiveFactureRepository->getAll());
        return $this->render('archive/facture/index.html.twig',[
            'factures' => $archiveFactureRepository->getAll()
        ]);
    }
}