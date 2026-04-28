<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    #[IsGranted('ROLE_USER')]
    public function index(ProductRepository $productRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart || empty($cart->getItems())) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }

        $items = $cart->getItems();
        $cartProducts = [];
        $subtotal = 0;

        foreach ($items as $item) {
            $product = $productRepo->find($item['productId']);
            if ($product) {
                $lineTotal = $product->getPrice() * $item['quantity'];
                $cartProducts[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $lineTotal,
                ];
                $subtotal += $lineTotal;
            }
        }

        // Frais de livraison (exemple fixe)
        $shipping = 4.90;
        $total = $subtotal + $shipping;

        return $this->render('checkout/index.html.twig', [
            'cartProducts' => $cartProducts,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    /* ========== À ACTIVER PLUS TARD ==========

    #[Route('/checkout/process', name: 'app_checkout_process', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function process(Request $request, EntityManagerInterface $em, ProductRepository $productRepo): Response
    {
        $user = $this->getUser();
        $cart = $user->getCart();

        if (!$cart || empty($cart->getItems())) {
            return $this->redirectToRoute('app_cart');
        }

        // Créer la commande
        $order = new \App\Entity\Order();
        $order->setBuyer($user);
        $order->setItems($cart->getItems());
        $order->setTotal($this->calculateTotal($cart, $productRepo));
        $order->setStatus('pending');
        $order->setCreatedAt(new \DateTimeImmutable());

        $em->persist($order);

        // Vider le panier
        $cart->setItems([]);
        $cart->setUpdatedAt(new \DateTimeImmutable());

        $em->flush();

        $this->addFlash('success', 'Commande passée avec succès !');
        return $this->redirectToRoute('app_checkout_confirmation', ['id' => $order->getId()]);
    }

    #[Route('/checkout/confirmation/{id}', name: 'app_checkout_confirmation')]
    #[IsGranted('ROLE_USER')]
    public function confirmation(\App\Entity\Order $order): Response
    {
        if ($order->getBuyer() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('checkout/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    private function calculateTotal($cart, ProductRepository $productRepo): int
    {
        $total = 0;
        foreach ($cart->getItems() as $item) {
            $product = $productRepo->find($item['productId']);
            if ($product) {
                $total += $product->getPrice() * $item['quantity'];
            }
        }
        return (int) round(($total + 4.90) * 100); // en centimes
    }

    ========== FIN À ACTIVER PLUS TARD ========== */
}
