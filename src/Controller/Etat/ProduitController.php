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

    #[Route('/valeur/stock', name: 'app_etat_produit_valeur',methods: ['GET'])]
    public function valeur()
    {
        $produits = $this->produitRepository->getAllByStockMoreThanZero();

        $stocks=[];
        foreach ($produits as $produit){
            $benefice = ((int)$produit->getPrixVente() - (int)$produit->getPrixAchat()) * (int) $produit->getStock();
            $stocks[]=[
                'id' => $produit->getId(),
                'reference' => $produit->getReference(),
                'codebarre' => $produit->getCodebarre(),
                'libelle' => $produit->getLibelle(),
                'prixAchat' => $produit->getPrixAchat(),
                'prixVente' => $produit->getPrixVente(),
                'stock' => $produit->getStock(),
                'benefice' => $benefice,
                'categorie' => $produit->getCategorie()->getLibelle(),
            ];
        }

        return $this->render('etat/produit/valeur.html.twig',[
            'produits' => $stocks
        ]);
    }
}
