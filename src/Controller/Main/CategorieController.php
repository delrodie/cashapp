<?php

namespace App\Controller\Main;

use App\Entity\Main\Categorie;
use App\Form\Main\CategorieType;
use App\Repository\Main\CategorieRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    public function __construct(private Utilities $utilities)
    {
    }

    #[Route('/', name: 'app_main_categorie_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug($this->utilities->slug($categorie->getLibelle()));
            $categorie->setCode($this->utilities->codeCategorie($categorie));
            $categorieRepository->save($categorie, true);

            notyf()->addSuccess("La categorie '{$categorie->getLibelle()} a été ajoutée avec succès!");

            return $this->redirectToRoute('app_main_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categorie' => $categorie,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_main_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug($this->utilities->slug($categorie->getLibelle()));
            $categorie->setCode($this->utilities->codeCategorie($categorie, true));
            $categorieRepository->save($categorie, true);

            notyf()->addSuccess("La catégorie {$categorie->getLibelle()} a été ajoutée avec succès!");

            return $this->redirectToRoute('app_main_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/categorie/new.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('main/categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie->setSlug($this->utilities->slug($categorie->getLibelle()));
            $categorie->setCode($this->utilities->codeCategorie($categorie, true));
            $categorieRepository->save($categorie, true);

            notyf()->addSuccess("La catégorie {$categorie->getLibelle()} a été modifiée avec succès!");

            return $this->redirectToRoute('app_main_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/categorie/edit.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'categorie' => $categorie,
            'form' => $form,
            'suppression' => true
        ]);
    }

    #[Route('/{id}', name: 'app_main_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $categorieRepository->remove($categorie, true);

            notyf()->addSuccess("La catégorie {$categorie->getLibelle()} a été supprimée avec succès!");
        }

        return $this->redirectToRoute('app_main_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
