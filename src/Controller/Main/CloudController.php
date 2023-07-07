<?php

namespace App\Controller\Main;

use App\Entity\Main\Cloud;
use App\Form\Main\CloudType;
use App\Repository\Main\CloudRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/main/cloud')]
class CloudController extends AbstractController
{
    #[Route('/', name: 'app_main_cloud_index', methods: ['GET','POST'])]
    public function index(Request $request, CloudRepository $cloudRepository): Response
    {
        $cloud = new Cloud();
        $form = $this->createForm(CloudType::class, $cloud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cloudRepository->save($cloud, true);

            return $this->redirectToRoute('app_main_cloud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/cloud/index.html.twig', [
            'clouds' => $cloudRepository->findAll(),
            'cloud' => $cloud,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_main_cloud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CloudRepository $cloudRepository): Response
    {
        $cloud = new Cloud();
        $form = $this->createForm(CloudType::class, $cloud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cloudRepository->save($cloud, true);

            return $this->redirectToRoute('app_main_cloud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/cloud/new.html.twig', [
            'cloud' => $cloud,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_cloud_show', methods: ['GET'])]
    public function show(Cloud $cloud): Response
    {
        return $this->render('main/cloud/show.html.twig', [
            'cloud' => $cloud,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_cloud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cloud $cloud, CloudRepository $cloudRepository): Response
    {
        $form = $this->createForm(CloudType::class, $cloud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cloudRepository->save($cloud, true);

            return $this->redirectToRoute('app_main_cloud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/cloud/edit.html.twig', [
            'clouds' => $cloudRepository->findAll(),
            'cloud' => $cloud,
            'form' => $form,
            'suppression' => true
        ]);
    }

    #[Route('/{id}', name: 'app_main_cloud_delete', methods: ['POST'])]
    public function delete(Request $request, Cloud $cloud, CloudRepository $cloudRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cloud->getId(), $request->request->get('_token'))) {
            $cloudRepository->remove($cloud, true);
        }

        return $this->redirectToRoute('app_main_cloud_index', [], Response::HTTP_SEE_OTHER);
    }
}
