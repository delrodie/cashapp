<?php

namespace App\Controller\Archive;

use App\Repository\Archive\ArchiveCategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/archive/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: "app_archive_categorie_index")]
    public function index(ArchiveCategorieRepository $archiveCategorieRepository)
    {
        return $this->render('archive/categorie/index.html.twig',[
            'categories' => $archiveCategorieRepository->getAll()
        ]);
    }
}