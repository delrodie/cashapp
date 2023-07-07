<?php

namespace App\Controller\Archive;

use App\Entity\Archive\Domaine;
use App\Form\Archive\DomaineType;
use App\Repository\Archive\ArchiveDomaineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/archive/domaine')]
class DomaineController extends AbstractController
{
    public function __construct(private ArchiveDomaineRepository $archiveDomaineRepository)
    {
    }

    #[Route('/', name: 'app_archive_domaine_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $domaines = $this->archiveDomaineRepository->findAll();

//        dd($domaines);

        return $this->render('archive/domaine/index.html.twig', [
            'domaines' => $this->archiveDomaineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_archive_domaine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $domaine = new Domaine();
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($domaine);
            $entityManager->flush();

            return $this->redirectToRoute('app_archive_domaine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('archive/domaine/new.html.twig', [
            'domaine' => $domaine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_archive_domaine_show', methods: ['GET'])]
    public function show(Domaine $domaine): Response
    {
        return $this->render('archive/domaine/show.html.twig', [
            'domaine' => $domaine,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_archive_domaine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Domaine $domaine, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_archive_domaine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('archive/domaine/edit.html.twig', [
            'domaine' => $domaine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_archive_domaine_delete', methods: ['POST'])]
    public function delete(Request $request, Domaine $domaine, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domaine->getId(), $request->request->get('_token'))) {
            $entityManager->remove($domaine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_archive_domaine_index', [], Response::HTTP_SEE_OTHER);
    }
}
