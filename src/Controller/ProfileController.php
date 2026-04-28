<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('profilePictureFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images/profiles',
                    $newFilename
                );
                $user->setProfilePicture($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
        ]);
    }

    // Upload photo depuis le profil (le petit "+")
    #[Route('/profile/upload-photo', name: 'app_profile_upload_photo', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function uploadPhoto(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $file = $request->files->get('profile_photo');

        if ($file) {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/images/profiles',
                $newFilename
            );
            $user->setProfilePicture($newFilename);
            $em->flush();
            $this->addFlash('success', 'Photo de profil mise à jour !');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/profile/{id}', name: 'app_profile_public', requirements: ['id' => '\d+'])]
    public function publicProfile(User $user): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/{id}/follow', name: 'app_follow_toggle', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function toggleFollow(User $user, EntityManagerInterface $em): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser === $user) {
            return $this->redirectToRoute('app_profile');
        }

        if ($currentUser->isFollowing($user)) {
            $currentUser->unfollow($user);
        } else {
            $currentUser->follow($user);
        }

        $em->flush();
        return $this->redirectToRoute('app_profile_public', ['id' => $user->getId()]);
    }
}