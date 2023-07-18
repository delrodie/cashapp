<?php

namespace App\Controller\Main;

use App\Entity\Main\Destockage;
use App\Form\Main\DestockageType;
use App\Repository\Main\DestockageRepository;
use App\Repository\Main\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/destockage')]
class DestockageController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/', name: 'app_main_destockage_index', methods: ['GET'])]
    public function index(DestockageRepository $destockageRepository): Response
    {
        return $this->render('main/destockage/index.html.twig', [
            'destockages' => $destockageRepository->findBy([],['createdAt' => 'desc']),
        ]);
    }

    #[Route('/new', name: 'app_main_destockage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DestockageRepository $destockageRepository): Response
    {

        return $this->renderForm('main/destockage/new.html.twig');
    }

    #[Route('/{id}', name: 'app_main_destockage_show', methods: ['GET'])]
    public function show(Destockage $destockage): Response
    {
        $produits=[];
        foreach ($destockage->getProduits() as $produit){
            $entity = $this->produitRepository->findOneBy(['id' => $produit['id']]);
            if ($entity){
                $produits[] = [
                    'id' => $entity->getId(),
                    'reference' => $entity->getReference(),
                    'libelle' => $entity->getLibelle(),
                    'prixAchat' => $entity->getPrixAchat(),
                    'prixVente' => $entity->getPrixVente(),
                    'oldPrixAchat' => $entity->getOldPrixAchat(),
                    'stock' => $entity->getStock(),
                    'quantite' => $produit['quantite'],
                    'montant' => $produit['montant'],
                ];
            }

        }
        return $this->render('main/destockage/show.html.twig', [
            'destockage' => $destockage,
            'produits' => $produits
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_destockage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Destockage $destockage, DestockageRepository $destockageRepository): Response
    {
        $form = $this->createForm(DestockageType::class, $destockage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $destockageRepository->save($destockage, true);

            return $this->redirectToRoute('app_main_destockage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/destockage/edit.html.twig', [
            'destockage' => $destockage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete/', name: 'app_main_destockage_delete', methods: ['GET','DELETE'])]
    public function delete(Request $request, Destockage $destockage, DestockageRepository $destockageRepository): Response
    {
        if (!$destockage){
            notyf()->addError("Echec: le destockage selectionné n'existe pas");
            return $this->redirectToRoute('app_main_destockage_index',[],Response::HTTP_SEE_OTHER);
        }
         // Mise à jour du stock des produits concernés
        foreach ($destockage->getProduits() as $produit){
            $entity = $this->produitRepository->findOneBy(['id' => $produit['id']]);

            // Si le produit n'existe pas renvoyer un message d'erreur
            if (!$entity){
                notyf()->addError("Echec: le produit {$produit['libelle']} n'a pas été trouvé! ");
                return $this->redirectToRoute('app_main_destockage_index', [], Response::HTTP_SEE_OTHER);
            }

            $stock = (int)$entity->getStock() + (int) $produit['quantite'];
            $entity->setStock($stock);

            $this->entityManager->persist($entity);
        }

        $this->entityManager->remove($destockage);
        $this->entityManager->flush();

        notyf()->addSuccess("Le destockage a été supprimer avec succès!");

        return $this->redirectToRoute('app_main_destockage_index', [], Response::HTTP_SEE_OTHER);
    }
}
