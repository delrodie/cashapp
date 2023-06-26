<?php

namespace App\Controller\Etat;

use App\Repository\Main\CategorieRepository;
use App\Repository\Main\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/etat/produit')]
class ProduitController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository, private CategorieRepository $categorieRepository
    )
    {
    }

    #[Route('/', name: 'app_etat_produit_index')]
    public function index(): Response
    {
        return $this->render('etat/produit/index.html.twig', [
            'produits' => $this->produitRepository->findListAll(),
        ]);
    }

    #[Route('/categorie', name: 'app_etat_produit_categorie', methods: ['GET'])]
    public function categorie()
    {
        return $this->render('etat/produit/categorie.html.twig',[
            'produits' => $this->produitRepository->findBy([],['categorie'=>"ASC"])
        ]);
    }
}
