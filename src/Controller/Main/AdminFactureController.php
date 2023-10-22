<?php

namespace App\Controller\Main;

use App\Repository\Main\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/facture')]
class AdminFactureController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository
    )
    {
    }

    #[Route('/', name:'app_main_admin_facture_index')]
    public function index()
    {
        return $this->render('main/facture/liste.html.twig',[
            'factures' => $this->factureRepository->findAllList()
        ]);
    }
}