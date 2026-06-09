<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\Main\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/produits')]
class ApiProduitController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository
    )
    {
    }

    #[Route('/', name: 'api_produits_datatables', methods: ['GET'])]
    public function apiDataTables(Request $request): JsonResponse
    {
        // 1. Récupérer les paramètres envoyés automatiquement par DataTables
        $draw = $request->query->getInt('draw', 1);
        $start = $request->query->getInt('start', 0);
        $length = $request->query->getInt('length', 10); // Nombre d'éléments par page
        $search = $request->query->all('search')['value'] ?? null;

        // Calcul de la page actuelle pour notre Paginator (commence à 1)
        $page = ($start / $length) + 1;

        // 2. Demander au Repository les produits filtrés et paginés
        // Nous allons créer cette méthode juste après
        $paginator = $this->produitRepository->findDataTablesProduits($page, $length, $search);

        $totalRecords = count($paginator); // Total sans filtre

        // 3. Formater les données pour le tableau JSON
        $data = [];
        foreach ($paginator as $produit) {
            $data[] = [
                'reference' => $produit->getReference(),
                'codebarre' => $produit->getCodebarre(),
                'libelle' => $produit->getLibelle(),
                'prixAchat' => $produit->getPrixAchat(),
                'prixVente' => $produit->getPrixVente(),
                'stock' => $produit->getStock(),
                'categorie' => $produit->getCategorie()?->getLibelle() ?? 'Non défini',
                // Boutons d'actions générés proprement
//                <a href="/produit/'.$produit->getId().'/edit" class="btn btn-sm btn-warning">Modifier</a>

                'actions' => '
                <a href="/etiquette/'.$produit->getId().'" class="text-center" target="_blank"><i class="ti-printer"></i></a>
            '
            ];
        }

        // 4. Réponse standardisée attendue par DataTables
        return new JsonResponse([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords, // Idéalement, calculez le total après filtre si recherche active
            "data" => $data
        ]);
    }

    #[Route('/choix', name: 'api_produits_datatables_choix', methods: ['GET'])]
    public function choix(Request $request): JsonResponse
    {
        // 1. Récupérer les paramètres envoyés automatiquement par DataTables
        $draw = $request->query->getInt('draw', 1);
        $start = $request->query->getInt('start', 0);
        $length = $request->query->getInt('length', 10); // Nombre d'éléments par page
        $search = $request->query->all('search')['value'] ?? null;

        // Calcul de la page actuelle pour notre Paginator (commence à 1)
        $page = ($start / $length) + 1;

        // 2. Demander au Repository les produits filtrés et paginés
        // Nous allons créer cette méthode juste après
        $paginator = $this->produitRepository->findDataTablesProduits($page, $length, $search);

        $totalRecords = count($paginator); // Total sans filtre

        // 3. Formater les données pour le tableau JSON
        $data = [];
        foreach ($paginator as $produit) {
            $data[] = [
                'checkbox' => '<input type="checkbox" class="produit-checkbox" value="'.$produit->getId().'" />',
                'id'        => $produit->getId(),
                'reference' => $produit->getReference(),
                'codebarre' => $produit->getCodebarre(),
                'libelle'   => $produit->getLibelle(),
                'prixAchat' => $produit->getPrixAchat(),
                'prixVente' => $produit->getPrixVente(),
                'stock'     => $produit->getStock(),
                'categorie' => $produit->getCategorie()?->getLibelle() ?? 'Non défini',
                'actions'   => '
                    <a href="/etiquette/'.$produit->getId().'" class="text-center" target="_blank"><i class="ti-printer"></i></a>
                ',
            ];
        }

        // 4. Réponse standardisée attendue par DataTables
        return new JsonResponse([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords, // Idéalement, calculez le total après filtre si recherche active
            "data" => $data
        ]);
    }
}
