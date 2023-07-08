<?php

namespace App\Controller\Etat;

use App\Repository\Archive\ArchiveProduitRepository;
use App\Repository\Main\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/etat/stock')]
class EtatStockController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository, private ArchiveProduitRepository $archiveProduitRepository
    )
    {
    }

    #[Route('/', name: 'app_etat_stock_index', methods: ['GET','POST'])]
    public function index(Request $request): Response
    {
        $query = (int) $request->get('querySeuil');
        $seuil = $query ?? 0;

        return $this->render('etat/etat_stock/index.html.twig', [
            'produits' => $this->association($seuil),
            'seuil' => $seuil
        ]);
    }

    public function association(int $seuil = null)
    {
        // Nouveaux produits
        $nouveauProduits = $this->produitRepository->findListAll();
        $nouveaux=[];
        foreach ($nouveauProduits as $produit){
            $nouveaux[]=[
                'libelle' => $produit->getLibelle(),
                'prixAchat' => $produit->getPrixAchat(),
                'prixVente' => $produit->getPrixVente(),
                'stock' => $produit->getStock(),
                'categorie' => $produit->getCategorie()->getLibelle(),
                'reference' => $produit->getReference()
            ];
        }

        // Anciens produits
        $ancienProduits = $this->archiveProduitRepository->getAll();
        $anciens=[];
        foreach ($ancienProduits as $produit){
            $anciens[] = [
                'libelle' => $produit->getLibelle(),
                'prixAchat' => $produit->getPrixachat(),
                'prixVente' => $produit->getPrixvente(),
                'stock' => $produit->getStock(),
                'categorie' => $produit->getCategorie()->getLibelle(),
                'reference' => $produit->getReference()
            ];
        }

        // Fusion, suppression des doublons et tri par catégorie
        $produits = array_merge($anciens, $nouveaux);
        $produitSansDoublons = array_reduce($produits, function ($carry, $produit){
            $libelle = $produit['libelle'];
            if (!isset($carry[$libelle])){
                $carry[$libelle] = $produit;
            }

            return $carry;
        });

        // Filtre des produits relativement au stock
        $limit = $seuil ?? 3;
        $produitsFiltres = array_filter($produitSansDoublons, function ($produit) use ($limit) {
            return $produit['stock'] <= $limit;
        });

        // Trier par catégorie
        usort($produitsFiltres, function ($a, $b) {
            return strcmp($a['categorie'], $b['categorie']);
        });
//        dd($produitsFiltres);

        return $produitsFiltres;

    }
}
