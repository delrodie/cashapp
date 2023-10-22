<?php

namespace App\Controller\Main;

use App\Entity\Main\Produit;
use App\Form\Main\ProduitType;
use App\Repository\Main\ProduitRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/produit')]
#[isGranted('ROLE_GERANT')]
class ProduitController extends AbstractController
{
    public function __construct(private Utilities $utilities)
    {
    }

    #[Route('/', name: 'app_main_produit_index', methods: ['GET','POST'])]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->utilities->slug($produit->getLibelle());
            $exist = $produitRepository->findOneBy(['slug'=>$slug]);
            if ($exist){
                notyf()->addError("Echec! ce produit existe déjà sous le code: {$exist->getReference()}!");
                return $this->redirectToRoute('app_main_produit_index',[], Response::HTTP_SEE_OTHER);
            }

            $produit->setReference($this->utilities->referenceProduit($produit));
            $produit->setLibelle(strtoupper($produit->getLibelle()));
            $produit->setSlug($slug);
            $produitRepository->save($produit, true);

            notyf()->addSuccess("Le produit {$produit->getLibelle()} a été ajouté avec succès!");

            return $this->redirectToRoute('app_main_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
            'produit' => $produit,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_main_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($produit);
            $produitRepository->save($produit, true);

            return $this->redirectToRoute('app_main_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('main/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setReference($this->utilities->referenceProduit($produit, true));
            $produit->setLibelle(strtoupper($produit->getLibelle()));
            $produit->setSlug($this->utilities->slug($produit->getLibelle()));
            $produitRepository->save($produit, true);

            notyf()->addSuccess("Le produit {$produit->getLibelle()} a été modifié avec succès!");

            return $this->redirectToRoute('app_main_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/produit/edit.html.twig', [
            'produits' => $produitRepository->findAll(),
            'produit' => $produit,
            'form' => $form,
            'suppression' => true,
        ]);
    }

    #[Route('/{id}', name: 'app_main_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit, true);
        }

        notyf()->addSuccess("Le produit {$produit->getLibelle()} a été supprimé avec succès!");

        return $this->redirectToRoute('app_main_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
