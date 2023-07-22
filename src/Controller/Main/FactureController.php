<?php

namespace App\Controller\Main;

use App\Entity\Main\Facture;
use App\Repository\Main\FactureRepository;
use App\Service\Gestion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/facture')]
class FactureController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository,
        private Gestion $gestion
    )
    {
    }

    #[Route('/', name: 'app_main_facture_index')]
    public function index(): Response
    {
        return $this->render('main/facture/index.html.twig');
    }

    #[Route('/print', name: 'app_main_facture_print', methods: ['GET'])]
    public function print(Request $request)
    {
        $data = $request->query->get('client');
        $facture = $this->factureRepository->findOneBy(['id' => (int)$data]);

        if (!$facture) {
            notyf()->addSuccess("La facture n'a pas été trouvée");
            return $this->redirectToRoute('app_main_facture_index', Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/facture/print.html.twig',[
            'facture' => $facture
        ]);
    }

    #[Route('/{id}/details', name: 'app_main_facture_details', methods: ['GET'])]
    public function details(Facture $facture): Response
    {
        return $this->render('main/facture/details.html.twig',[
            'facture' => $facture
        ]);
    }

    #[Route('/{id}', name: 'app_main_facture_suppression',methods: ['POST'])]
    public function suppression(Request $request, Facture $facture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $this->gestion->supFacture($facture);
        }

        notyf()->position('x', 'center')
            ->position('y', 'top')
            ->addSuccess("La facture a été supprimée avec succès");

        return $this->redirectToRoute('app_main_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
