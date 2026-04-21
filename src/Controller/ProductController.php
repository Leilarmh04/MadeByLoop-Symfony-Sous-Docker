<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    // --- Page produit (déjà existante) ---
    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    // --- Mes produits (liste du vendeur) ---
    #[Route('/my-products', name: 'app_my_products')]
    #[IsGranted('ROLE_USER')]
    public function myProducts(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $products = $user->getProducts();

        return $this->render('product/my_products.html.twig', [
            'products' => $products,
        ]);
    }

    // --- Ajouter un produit ---
    #[Route('/product/new', name: 'app_product_new', priority: 1)]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSeller($this->getUser());
            $product->setCreatedAt(new \DateTimeImmutable());

            // Gestion de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images/products',
                    $newFilename
                );
                $product->setImage($newFilename);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('app_my_products');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form,
        ]);
    }

    // --- Modifier un produit ---
    #[Route('/product/{id}/edit', name: 'app_product_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // Vérifier que c'est bien le vendeur
        if ($product->getSeller() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Ce produit ne vous appartient pas.');
        }

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images/products',
                    $newFilename
                );
                $product->setImage($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');
            return $this->redirectToRoute('app_my_products');
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    // --- Supprimer un produit ---
    #[Route('/product/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        if ($product->getSeller() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Ce produit ne vous appartient pas.');
        }

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }

        return $this->redirectToRoute('app_my_products');
    }
}