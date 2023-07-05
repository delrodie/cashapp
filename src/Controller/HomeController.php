<?php

namespace App\Controller;

use App\Service\Statistiques;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private Statistiques $statistiques)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $periode = [
            'debut' => date('Y-m-01'),
            'fin' => date('Y-m-31')
        ];

        return $this->render('home/index.html.twig',[
            'valeurTotal' => $this->statistiques->valeurStock(),
            'caisses' => $this->statistiques->recetteCaisse($periode['debut'],$periode['fin']),
            'factures' => $this->statistiques->dernieresFactures(5),
            'clients' => $this->statistiques->topClient(),
            'recetteTotale' => $this->statistiques->recetteTotalParMois()
        ]);
    }

    #[Route('/template', name: 'app_home_template')]
    public function template()
    {
        return $this->render('home/template.html.twig');
    }
}
