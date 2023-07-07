<?php

namespace App\Controller\Archive;

use App\Repository\Archive\ArchiveProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/archive/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_archive_produit_index')]
    public function index(ArchiveProduitRepository $archiveProduitRepository)
    {
//        dd($archiveProduitRepository->findAll());
        return $this->render('archive/produit/index.html.twig',[
            'produits' => $archiveProduitRepository->getAll()
        ]);
    }
}