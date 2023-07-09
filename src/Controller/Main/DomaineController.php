<?php

namespace App\Controller\Main;

use App\Entity\Main\Domaine;
use App\Form\Main\DomaineType;
use App\Repository\Main\DomaineRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/domaine')]
#[isGranted('ROLE_GERANT')]
class DomaineController extends AbstractController
{
    public function __construct(private Utilities $utilities)
    {
    }

    #[Route('/', name: 'app_main_domaine_index', methods: ['GET','POST'])]
    public function index(Request $request, DomaineRepository $domaineRepository): Response
    {
        $domaine = new Domaine();
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domaine->setCode($this->utilities->codeDomaine());
            $domaine->setSlug($this->utilities->slug($domaine->getLibelle()));
            $domaineRepository->save($domaine, true);

            notyf()->addSuccess("Le domaine '{$domaine->getLibelle()}' à été ajouté avec succès!");

            return $this->redirectToRoute('app_main_domaine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('main/domaine/index.html.twig', [
            'domaines' => $domaineRepository->findAll(),
            'domaine' => $domaine,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_main_domaine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DomaineRepository $domaineRepository): Response
    {
        $domaine = new Domaine();
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domaineRepository->save($domaine, true);

            return $this->redirectToRoute('app_main_domaine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/domaine/new.html.twig', [
            'domaine' => $domaine,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_domaine_show', methods: ['GET'])]
    public function show(Domaine $domaine): Response
    {
        return $this->render('main/domaine/show.html.twig', [
            'domaine' => $domaine,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_domaine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Domaine $domaine, DomaineRepository $domaineRepository): Response
    {
        $form = $this->createForm(DomaineType::class, $domaine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $domaine->setSlug($this->utilities->slug($domaine->getLibelle()));
            $domaineRepository->save($domaine, true);

            notyf()->addSuccess("Le domaine {$domaine->getLibelle()} a été modifié avec succès!");

            return $this->redirectToRoute('app_main_domaine_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/domaine/edit.html.twig', [
            'domaines' => $domaineRepository->findAll(),
            'domaine' => $domaine,
            'form' => $form,
            'suppression' => true
        ]);
    }

    #[Route('/{id}', name: 'app_main_domaine_delete', methods: ['POST'])]
    public function delete(Request $request, Domaine $domaine, DomaineRepository $domaineRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domaine->getId(), $request->request->get('_token'))) {
            // Si le domaine est concerné par des catégories alors renvoie au show
//            dd(!count($domaine->getCategories()));
            if (count($domaine->getCategories()) > 0){
                notyf()->addError("Echèc: le domaine {$domaine->getLibelle()} contient des catégories. Veuillez les supprimer d'abord.");
                return $this->redirectToRoute('app_main_domaine_show',['id'=>$domaine->getId()]);
            }
            $domaineRepository->remove($domaine, true);

            notyf()->addSuccess("Le domaine {$domaine->getLibelle()} a été supprimé avec succès!");
        }

        return $this->redirectToRoute('app_main_domaine_index', [], Response::HTTP_SEE_OTHER);
    }
}
