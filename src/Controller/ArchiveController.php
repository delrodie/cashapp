<?php

namespace App\Controller;

use App\Service\Archives;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/archives')]
class ArchiveController extends AbstractController
{
    public function __construct(private Archives $archives)
    {
    }

    #[Route('/', name: 'app_archive_index')]
    public function index()
    {
//        dd($this->archives->bilan());
        return $this->render('archive/statistiques.html.twig',[
            'bilans' => $this->archives->bilan()
        ]);
    }
}