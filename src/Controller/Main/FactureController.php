<?php

namespace App\Controller\Main;

use App\Repository\Main\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/facture')]
class FactureController extends AbstractController
{
    public function __construct(private FactureRepository $factureRepository)
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
}
