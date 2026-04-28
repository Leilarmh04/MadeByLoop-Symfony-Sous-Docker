<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Product;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages')]
    #[IsGranted('ROLE_USER')]
    public function index(ConversationRepository $conversationRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $conversations = $conversationRepo->createQueryBuilder('c')
            ->where('c.buyer = :user OR c.seller = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
            'activeConversation' => null,
        ]);
    }

    #[Route('/messages/{id}', name: 'app_messages_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Conversation $conversation, Request $request, EntityManagerInterface $em, ConversationRepository $conversationRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($conversation->getBuyer() !== $user && $conversation->getSeller() !== $user) {
            throw $this->createAccessDeniedException('Cette conversation ne vous appartient pas.');
        }

        // Marquer les messages comme lus
        foreach ($conversation->getMessages() as $message) {
            if ($message->getSender() !== $user && !$message->isRead()) {
                $message->setIsRead(true);
            }
        }
        $em->flush();

        // Envoyer un message
        if ($request->isMethod('POST')) {
            $content = trim($request->request->get('content', ''));
            if ($content !== '') {
                $message = new Message();
                $message->setConversation($conversation);
                $message->setSender($user);
                $message->setContent($content);
                $message->setSentAt(new \DateTimeImmutable());
                $message->setIsRead(false);

                $em->persist($message);
                $em->flush();

                return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
            }
        }

        $allConversations = $conversationRepo->createQueryBuilder('c')
            ->where('c.buyer = :user OR c.seller = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('message/index.html.twig', [
            'conversations' => $allConversations,
            'activeConversation' => $conversation,
        ]);
    }

    #[Route('/contact-seller/{id}', name: 'app_contact_seller')]
    #[IsGranted('ROLE_USER')]
    public function contactSeller(Product $product, EntityManagerInterface $em, ConversationRepository $conversationRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $seller = $product->getSeller();

        if ($user === $seller) {
            $this->addFlash('error', 'Vous ne pouvez pas vous contacter vous-même.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Chercher une conversation existante
        $conversation = $conversationRepo->createQueryBuilder('c')
            ->where('(c.buyer = :user AND c.seller = :seller) OR (c.buyer = :seller AND c.seller = :user)')
            ->setParameter('user', $user)
            ->setParameter('seller', $seller)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setBuyer($user);
            $conversation->setSeller($seller);
            $conversation->setCreatedAt(new \DateTimeImmutable());
            $conversation->addProduct($product);
            $em->persist($conversation);
            $em->flush();
        }

        return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
    }

    #[Route('/contact-user/{id}', name: 'app_contact_user')]
    #[IsGranted('ROLE_USER')]
    public function contactUser(User $targetUser, EntityManagerInterface $em, ConversationRepository $conversationRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user === $targetUser) {
            return $this->redirectToRoute('app_profile');
        }

        $conversation = $conversationRepo->createQueryBuilder('c')
            ->where('(c.buyer = :user AND c.seller = :target) OR (c.buyer = :target AND c.seller = :user)')
            ->setParameter('user', $user)
            ->setParameter('target', $targetUser)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setBuyer($user);
            $conversation->setSeller($targetUser);
            $conversation->setCreatedAt(new \DateTimeImmutable());
            $em->persist($conversation);
            $em->flush();
        }

        return $this->redirectToRoute('app_messages_show', ['id' => $conversation->getId()]);
    }
}