<?php

namespace App\Controller;

use App\Form\ProductFormType;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/profile/product/new', name: 'app_product_new')]
    #[IsGranted('ROLE_USER')]
    public function newProduct(Request $request, EntityManagerInterface $em): Response
{
    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    // Vérifie que l'utilisateur est bien vendeur
    if (!in_array($user->getSellerRole(), ['seller', 'both'])) {
        throw $this->createAccessDeniedException('Vous devez être vendeur pour ajouter un produit.');
    }

    $product = new Product();
    $form = $this->createForm(ProductFormType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('kernel.project_dir') . '/public/images/products',
                $newFilename
            );
            $product->setImage($newFilename);
        }

        $product->setSeller($user);
        $product->setCreatedAt(new \DateTimeImmutable());

        $em->persist($product);
        $em->flush();

        $this->addFlash('success', 'Produit ajouté avec succès !');
        return $this->redirectToRoute('app_profile');
    }

    return $this->render('profile/new_product.html.twig', [
        'productForm' => $form->createView(),
    ]);
}
}