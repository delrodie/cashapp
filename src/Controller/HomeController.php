<?php

namespace App\Controller;

use App\Service\NewStatistiques;
use App\Service\Statistiques;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private Statistiques $statistiques,
        private NewStatistiques $newStatistiques
    )
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if ($this->getUser()->getRoles()[0] === 'ROLE_CAISSE') {
            return $this->redirectToRoute('app_main_facture_index',[],Response::HTTP_SEE_OTHER);
        }
        $periode = [
            'debut' => date('Y-m-01'),
            'fin' => date('Y-m-31')
        ];

        return $this->render('home/index.html.twig',[
            'valeurTotal' => $this->newStatistiques->valeurStock(),
            'caisses' => $this->newStatistiques->recetteCaisse($periode['debut'],$periode['fin']),
            'factures' => [], // $this->newStatistiques->dernieresFactures(5),
            'clients' => [], // $this->statistiques->topClient(),
            'recetteTotale' => $this->newStatistiques->recetteTotalParMois()
        ]);
    }

    #[Route('/template', name: 'app_home_template')]
    public function template()
    {
        return $this->render('home/template.html.twig');
    }
}
