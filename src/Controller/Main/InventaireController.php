<?php

namespace App\Controller\Main;

use App\Repository\Main\InventaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/inventaire')]
class InventaireController extends AbstractController
{

    public function __construct(
        private InventaireRepository $inventaireRepository
    )
    {
    }

    #[Route('/', name: 'app_main_inventaire_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $date = $request->get('date');

        $inventaires = $this->inventaireRepository->findBy([],['date' => "DESC"]);
        if ($date){
            $inventaires = $this->inventaireRepository->findBy(['date' => $date], ['id'=>"DESC"]);
        }

        return $this->render('main/inventaire/liste.html.twig',[
            'inventaires' => $inventaires
        ]);
    }

    #[Route('/create', name: 'app_main_inventaire_create')]
    public function create()
    {
        return $this->render('main/inventaire/index.html.twig');
    }

    #[Route('/{date}/details', name: "app_main_inventaire_details", methods: ['GET'])]
    public function details($date)
    {
        return $this->render('main/inventaire/details.html.twig',[
            'inventaires' => $this->inventaireRepository->findBy(['date' => $date])
        ]);
    }

    #[Route('/delete', name: 'app_main_inventaire_delete')]
    public function delete()
    {

    }
}