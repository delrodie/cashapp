<?php

namespace App\Controller\Etat;

use App\Repository\Main\FactureRepository;
use App\Service\Statistiques;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/etat/finances')]
#[isGranted('ROLE_GERANT')]
class EtatFinanceController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository, private Statistiques $statistiques
    )
    {
    }

    #[Route('/', name: 'app_etat_finance_recette')]
    public function recette(): Response
    {

        $recettes = $this->statistiques->recetteJournaliere();
        $montantTotal = 0;
        foreach ($recettes as $recette){
            $montantTotal += (int) $recette['totalMontant'];
        }

        return $this->render('etat/etat_finance/index.html.twig', [
            'recettes' => $this->statistiques->recetteJournaliere(),
            'montantTotal' => $montantTotal,
        ]);
    }
}
