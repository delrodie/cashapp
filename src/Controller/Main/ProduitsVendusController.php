<?php

namespace App\Controller\Main;

use App\Repository\Main\FactureRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/produits-vendus')]
#[isGranted('ROLE_GERANT')]
class ProduitsVendusController extends AbstractController
{
    public function __construct(
        private readonly FactureRepository $factureRepository, 
        private readonly Utilities $utility
    )
    {
        
    }

    #[Route('/', name:"app_produits_vendus", methods:['GET','POST'])]
    public function search(Request $request)
    {
        $searchQuery = $request->query->get('produits');
        $produits = [];

        if ($searchQuery) { 
           $produits = $this->utility->findProduitsVendus($searchQuery);
        }

        return $this->render('main/facture/produits_vendus.html.twig',[
            'produits' => $produits
        ]);
    }
}