<?php

declare(strict_types=1);

namespace App\Controller\Main;

use App\Service\CalculBenefice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/benefice')]
class BeneficeController extends AbstractController
{
    public function __construct(private readonly CalculBenefice $benefice)
    {
    }

    #[Route('/', name: 'app_main_benefice_index')]
    public function index(): Response
    {
        $beneficeParFacture = $this->benefice->beneficeParFacture();
        return $this->render('benefice/index.html.twig',[
            'beneficeParJour' => $this->benefice->beneficeParJour($beneficeParFacture),
            'benefices' => $this->benefice->beneficeParMois($beneficeParFacture)
        ]);
    }

    #[Route('/mois', name:'app_main_benefice_mois')]
    public function mois(): Response
    {
        $beneficeParFacture = $this->benefice->beneficeParFacture();
        return $this->render('benefice/mois.html.twig',[
            'benefices' => $this->benefice->beneficeParMois($beneficeParFacture)
        ]);
    }

    #[Route('/annee/?', name: 'app_main_benefice_annee')]
    public function annee(): Response
    {
        $beneficeParFacture = $this->benefice->beneficeParFacture();
        return $this->render('benefice/annee.html.twig',[
            'benefices' => $this->benefice->beneficeParAn($beneficeParFacture)
        ]);
    }
}
