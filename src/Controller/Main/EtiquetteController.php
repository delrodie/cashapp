<?php

declare(strict_types=1);

namespace App\Controller\Main;

use App\Entity\Main\Produit;
use App\Repository\Main\ProduitRepository;
use App\Service\LabelPrinterService;
use App\Service\ZplService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/etiquette')]
class EtiquetteController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface       $entityManager,
        private ZplService                   $zplService,
        private LabelPrinterService          $printerService,
        private readonly LabelPrinterService $labelPrinterService, private readonly ProduitRepository $produitRepository
    )
    {
    }

    #[Route('/', name: 'app_main_etiquette_list')]
    public function index(): Response
    {
//        return $this->render('etat/produit/impression_list.html.twig');
        return $this->render('etat/etiquette/liste_produits2.html.twig');
    }

    #[Route('/{id}', name: 'app_main_etiquette_print', methods: ['GET'])]
    public function print(Produit $produit): Response
    {
        return $this->render('etat/etiquette/print_label.html.twig',[
            'produit' => $produit
        ]);
    }

    #[Route('/multiple/print', name: 'app_main_etiquette_multiple', methods: ['GET'])]
    public function multiple(Request $request): Response
    {
        $requestIds = $request->query->get('ids');
        if (!$requestIds) {
            notyf("Aucun produit sélectionné");
            return $this->redirectToRoute('app_main_etiquette_list');
        }

        $arrayIds = explode(',', $requestIds);
        $produits=[];$i=0;
        foreach ($arrayIds as $id) {
            $produits[$i++] = $this->produitRepository->findOneById($id);
        }

        return $this->render('etat/etiquette/print_multiple_label.html.twig',[
            'produits' => $produits
        ]);
    }

}
