<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    #[Route('/cart', name: 'app_cart')]
    #[IsGranted('ROLE_USER')]
    public function index(ProductRepository $productRepo): Response
    {
        $cart = $this->getOrCreateCart();
        $items = $cart->getItems();

        $cartProducts = [];
        $total = 0;

        foreach ($items as $item) {
            $product = $productRepo->find($item['productId']);
            if ($product) {
                $subtotal = $product->getPrice() * $item['quantity'];
                $cartProducts[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }

        return $this->render('cart/index.html.twig', [
            'cartProducts' => $cartProducts,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    #[IsGranted('ROLE_USER')]
    public function add(Product $product): Response
    {
        $cart = $this->getOrCreateCart();
        $items = $cart->getItems();

        $found = false;
        foreach ($items as &$item) {
            if ($item['productId'] === $product->getId()) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $items[] = ['productId' => $product->getId(), 'quantity' => 1];
        }

        $cart->setItems($items);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirect($_SERVER['HTTP_REFERER'] ?? $this->generateUrl('app_cart'));
    }

    // +1 quantité
    #[Route('/cart/increment/{id}', name: 'app_cart_increment')]
    #[IsGranted('ROLE_USER')]
    public function increment(Product $product): Response
    {
        $cart = $this->getOrCreateCart();
        $items = $cart->getItems();

        foreach ($items as &$item) {
            if ($item['productId'] === $product->getId()) {
                $item['quantity']++;
                break;
            }
        }

        $cart->setItems($items);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        return $this->redirectToRoute('app_cart');
    }

    // -1 quantité (supprime si tombe à 0)
    #[Route('/cart/decrement/{id}', name: 'app_cart_decrement')]
    #[IsGranted('ROLE_USER')]
    public function decrement(Product $product): Response
    {
        $cart = $this->getOrCreateCart();
        $items = $cart->getItems();

        $newItems = [];
        foreach ($items as $item) {
            if ($item['productId'] === $product->getId()) {
                $item['quantity']--;
                if ($item['quantity'] > 0) {
                    $newItems[] = $item;
                }
                // si quantity == 0, on ne l'ajoute pas => supprimé
            } else {
                $newItems[] = $item;
            }
        }

        $cart->setItems($newItems);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    #[IsGranted('ROLE_USER')]
    public function remove(Product $product): Response
    {
        $cart = $this->getOrCreateCart();
        $items = $cart->getItems();

        $items = array_values(array_filter($items, function ($item) use ($product) {
            return $item['productId'] !== $product->getId();
        }));

        $cart->setItems($items);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        $this->addFlash('success', 'Produit retiré du panier.');
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    #[IsGranted('ROLE_USER')]
    public function clear(): Response
    {
        $cart = $this->getOrCreateCart();
        $cart->setItems([]);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        $this->addFlash('success', 'Panier vidé.');
        return $this->redirectToRoute('app_cart');
    }

    private function getOrCreateCart(): Cart
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setItems([]);
            $cart->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $cart;
    }
}