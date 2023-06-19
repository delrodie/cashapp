<?php

namespace App\Controller\Main;

use App\Entity\Main\Fournisseur;
use App\Form\Main\FournisseurType;
use App\Repository\Main\FournisseurRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fournisseur')]
class FournisseurController extends AbstractController
{
    public function __construct(private Utilities $utilities)
    {
    }

    #[Route('/', name: 'app_main_fournisseur_index', methods: ['GET','POST'])]
    public function index(Request $request, FournisseurRepository $fournisseurRepository): Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fournisseur->setCode($this->utilities->codeFournisseur());
            $fournisseur->setNom(strtoupper($fournisseur->getNom()));
            $fournisseurRepository->save($fournisseur, true);

            notyf()->addSuccess("Le fournisseur '{$fournisseur->getNom()}' a été ajouté avec succès!");

            return $this->redirectToRoute('app_main_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/fournisseur/index.html.twig', [
            'fournisseurs' => $fournisseurRepository->findAll(),
            'fournisseur' => $fournisseur,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_main_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FournisseurRepository $fournisseurRepository): Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fournisseurRepository->save($fournisseur, true);

            return $this->redirectToRoute('app_main_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/fournisseur/new.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_fournisseur_show', methods: ['GET'])]
    public function show(Fournisseur $fournisseur): Response
    {
        return $this->render('main/fournisseur/show.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fournisseur $fournisseur, FournisseurRepository $fournisseurRepository): Response
    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fournisseur->setNom(strtoupper($fournisseur->getNom()));
            $fournisseurRepository->save($fournisseur, true);

            notyf()->addSuccess("Le fournisseur '{$fournisseur->getNom()}' a été modifié avec succès!");

            return $this->redirectToRoute('app_main_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/fournisseur/edit.html.twig', [
            'fournisseurs' => $fournisseurRepository->findAll(),
            'fournisseur' => $fournisseur,
            'form' => $form,
            'suppression' => true
        ]);
    }

    #[Route('/{id}', name: 'app_main_fournisseur_delete', methods: ['POST'])]
    public function delete(Request $request, Fournisseur $fournisseur, FournisseurRepository $fournisseurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fournisseur->getId(), $request->request->get('_token'))) {
            $fournisseurRepository->remove($fournisseur, true);

            notyf()->addSuccess("Le fournisseur '{$fournisseur->getNom()}' a été supprimé avec succès!");
        }

        return $this->redirectToRoute('app_main_fournisseur_index', [], Response::HTTP_SEE_OTHER);
    }
}
