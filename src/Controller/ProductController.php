<?php
 
namespace App\Controller;
 
use App\Entity\Product;
use App\Entity\Review;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
 
class ProductController extends AbstractController
{
    // --- Page produit avec avis + produits similaires ---
    #[Route('/product/{id}', name: 'app_product_show')]
    public function show(Product $product, Request $request, EntityManagerInterface $em, ProductRepository $productRepo): Response
    {
        // Gestion de la soumission d'un avis
        $reviewError = null;
        if ($request->isMethod('POST') && $this->getUser()) {
            $user = $this->getUser();
 
            // Vérifier que l'utilisateur n'est pas le vendeur
            if ($user === $product->getSeller()) {
                $reviewError = 'Vous ne pouvez pas laisser un avis sur votre propre produit.';
            } else {
                // Vérifier si l'utilisateur a déjà laissé un avis
                $existingReview = $em->getRepository(Review::class)->findOneBy([
                    'product' => $product,
                    'user' => $user,
                ]);
 
                if ($existingReview) {
                    $reviewError = 'Vous avez déjà laissé un avis sur ce produit.';
                } else {
                    $rating = (int) $request->request->get('rating');
                    $comment = trim($request->request->get('comment', ''));
 
                    if ($rating < 1 || $rating > 5) {
                        $reviewError = 'La note doit être entre 1 et 5.';
                    } else {
                        if ($this->isCsrfTokenValid('review' . $product->getId(), $request->request->get('_token'))) {
                            $review = new Review();
                            $review->setProduct($product);
                            $review->setUser($user);
                            $review->setRating($rating);
                            $review->setComment($comment ?: null);
                            $review->setCreatedAt(new \DateTimeImmutable());
 
                            $em->persist($review);
                            $em->flush();
 
                            $this->addFlash('success', 'Merci pour votre avis !');
                            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
                        }
                    }
                }
            }
        }
 
        // Calculer la note moyenne
        $reviews = $product->getReviews();
        $averageRating = 0;
        if (count($reviews) > 0) {
            $total = 0;
            foreach ($reviews as $review) {
                $total += $review->getRating();
            }
            $averageRating = round($total / count($reviews), 1);
        }
 
        // Vérifier si l'utilisateur connecté a déjà laissé un avis
        $userHasReviewed = false;
        if ($this->getUser()) {
            foreach ($reviews as $review) {
                if ($review->getUser() === $this->getUser()) {
                    $userHasReviewed = true;
                    break;
                }
            }
        }
 
        // Produits similaires (même catégorie, excluant le produit actuel)
        $similarProducts = $productRepo->findSimilarProducts($product, 4);
 
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'userHasReviewed' => $userHasReviewed,
            'similarProducts' => $similarProducts,
            'reviewError' => $reviewError,
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