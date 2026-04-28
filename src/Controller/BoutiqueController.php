<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('shop/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/boutique/{id}', name: 'app_user_boutique', requirements: ['id' => '\d+'])]
    public function userBoutique(User $user): Response
    {
        return $this->render('shop/user_boutique.html.twig', [
            'seller' => $user,
            'products' => $user->getProducts(),
        ]);
    }
}