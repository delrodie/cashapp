<?php

namespace App\Controller\Main;

use App\Entity\Main\User;
use App\Form\Main\UserType;
use App\Repository\Main\UserRepository;
use App\Service\Utilities;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher, private Utilities $utilities
    )
    {
    }

    #[Route('/', name: 'app_main_user_index', methods: ['GET','POST'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $userRepository->save($user, true);

            notyf()->addSuccess("Utilisateur '{$user->getUserIdentifier()}' a été ajouté avec succès! ");

            return $this->redirectToRoute('app_main_user_index', [], Response::HTTP_SEE_OTHER);
        }

//        dd($users);

        return $this->render('main/user/index.html.twig', [
            'users' => $this->utilities->getUsers('delrodie'),
            'user' => $user,
            'form' => $form,
            'suppression' => false
        ]);
    }

    #[Route('/new', name: 'app_main_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_main_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_main_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('main/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_main_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            $userRepository->save($user, true);

            notyf()->addSuccess("Utilisateur '{$user->getUserIdentifier()}' a été modifié avec succès! ");

            return $this->redirectToRoute('app_main_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('main/user/edit.html.twig', [
            'users' => $this->utilities->getUsers('delrodie'),
            'user' => $user,
            'form' => $form,
            'suppression' => true
        ]);
    }

    #[Route('/{id}', name: 'app_main_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);

            notyf()->addSuccess("L'utilisateur '{$user->getUserIdentifier()}' a été supprimé avec succès!");
        }

        return $this->redirectToRoute('app_main_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
