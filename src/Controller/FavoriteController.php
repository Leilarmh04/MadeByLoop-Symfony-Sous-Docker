<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FavoriteController extends AbstractController
{
    #[Route('/favorite/toggle/{id}', name: 'app_favorite_toggle')]
    #[IsGranted('ROLE_USER')]
    public function toggle(Product $product, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user->isFavorite($product)) {
            $user->removeFavorite($product);
        } else {
            $user->addFavorite($product);
        }

        $em->flush();

        return $this->redirect($_SERVER['HTTP_REFERER'] ?? $this->generateUrl('app_profile'));
    }

    #[Route('/my-favorites', name: 'app_my_favorites')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('favorite/index.html.twig', [
            'favorites' => $user->getFavorites(),
        ]);
    }
}