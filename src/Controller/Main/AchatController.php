<?php

namespace App\Controller\Main;

use App\Entity\Main\Achat;
use App\Repository\Main\AchatRepository;
use App\Repository\Main\FournisseurRepository;
use App\Repository\Main\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/achat')]
class AchatController extends AbstractController
{
    public function __construct(
        private AchatRepository $achatRepository, private FournisseurRepository $fournisseurRepository,
        private ProduitRepository $produitRepository, private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/', name: 'app_main_achat_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('main/achat/index.html.twig', [
            'achats' => $this->achatRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_main_achat_new', methods: ['GET','POST'])]
    public function new(Request $request)
    {
        return $this->render('main/achat/new.html.twig',[
            'fournisseurs' => $this->fournisseurRepository->findBy([],['nom'=>"ASC"])
        ]);
    }

    #[Route('/{id}', name: 'app_main_achat_show',methods: ['GET'])]
    public function show(Achat $achat)
    {
        return $this->render('main/achat/show.html.twig',[
            'achat' => $achat
        ]);
    }

    #[Route('/{id}/delete', name: 'app_main_achat_delete',methods: ['GET','DELETE'])]
    public function delete(Achat $achat)
    {
        if (!$achat){
            notyf()->addError("Echec: L'achat selectionné n'a pas été trouvé!");
            return $this->redirectToRoute('app_main_achat_index', [],Response::HTTP_SEE_OTHER);
        }

        foreach ($achat->getProduits() as $produit){
            $entity = $this->produitRepository->findOneBy(['id' => $produit['id']]);
            if (!$entity){
                notyf()->addError("Echec: le produit {$produit['libelle']} n'a pas été trouvé!");
                return $this->redirectToRoute('app_main_achat_index',[], Response::HTTP_SEE_OTHER);
            }

            $stock = (int)$entity->getStock() - (int)$produit['quantite'];
            if ($stock < 0){
                notyf()->addError("Echec, des produits de cet achat ont déjà été vendus!");
                return $this->redirectToRoute('app_main_achat_index',[], Response::HTTP_SEE_OTHER);
            }

            $entity->setPrixAchat($entity->getOldPrixAchat());
            $entity->setStock($stock);

            $this->entityManager->persist($entity);
        }

//        dd($achat);

        $this->entityManager->remove($achat);
        $this->entityManager->flush();

        notyf()->addSuccess("Achat supprimé avec succès!");

        return $this->redirectToRoute('app_main_achat_index',[], Response::HTTP_SEE_OTHER);
    }
}
