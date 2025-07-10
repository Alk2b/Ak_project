<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\CartItem;
use App\Entity\Purchase;
use App\Repository\GameRepository;
use App\Repository\CartItemRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GamePlatformController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(GameRepository $gameRepository): Response
    {
        $featuredGames = $gameRepository->findBy([], null, 3);
        
        return $this->render('index.html.twig', [
            'featured_games' => $featuredGames
        ]);
    }

    #[Route('/shop', name: 'app_shop')]
    public function shop(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();
        
        return $this->render('shop.html.twig', [
            'games' => $games
        ]);
    }

    #[Route('/cart', name: 'app_cart')]
    public function cart(Request $request, CartItemRepository $cartItemRepository): Response
    {
        $sessionId = $this->getSessionId($request);
        $cartItems = $cartItemRepository->findBy(['sessionId' => $sessionId]);
        
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->getGame()->getPrice() * $item->getQuantity();
        }
        
        return $this->render('cart.html.twig', [
            'cart_items' => $cartItems,
            'total' => $total
        ]);
    }
    
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function addToCart(Game $game, Request $request, EntityManagerInterface $em, CartItemRepository $cartItemRepository): Response
    {
        $sessionId = $this->getSessionId($request);
        
        $existingItem = $cartItemRepository->findOneBy([
            'game' => $game,
            'sessionId' => $sessionId
        ]);
        
        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setGame($game);
            $cartItem->setSessionId($sessionId);
            $cartItem->setQuantity(1);
            $em->persist($cartItem);
        }
        
        $em->flush();
        
        $this->addFlash('success', 'Le jeu "' . $game->getTitle() . '" a été ajouté au panier !');
        
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(CartItem $cartItem, Request $request, EntityManagerInterface $em): Response
    {
        $sessionId = $this->getSessionId($request);
        
        if ($cartItem->getSessionId() !== $sessionId) {
            $this->addFlash('error', 'Erreur: cet item ne vous appartient pas.');
            return $this->redirectToRoute('app_cart');
        }
        
        $gameName = $cartItem->getGame()->getTitle();
        $em->remove($cartItem);
        $em->flush();
        
        $this->addFlash('success', 'Le jeu "' . $gameName . '" a été retiré du panier.');
        
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decreaseFromCart(CartItem $cartItem, Request $request, EntityManagerInterface $em): Response
    {
        $sessionId = $this->getSessionId($request);
        
        if ($cartItem->getSessionId() !== $sessionId) {
            $this->addFlash('error', 'Erreur: cet item ne vous appartient pas.');
            return $this->redirectToRoute('app_cart');
        }
        
        if ($cartItem->getQuantity() > 1) {
            $cartItem->setQuantity($cartItem->getQuantity() - 1);
            $em->flush();
            $this->addFlash('success', 'Quantité mise à jour.');
        } else {
            $gameName = $cartItem->getGame()->getTitle();
            $em->remove($cartItem);
            $em->flush();
            $this->addFlash('success', 'Le jeu "' . $gameName . '" a été retiré du panier.');
        }
        
        return $this->redirectToRoute('app_cart');
    }

    private function getSessionId(Request $request): string
    {
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }
        
        $cartId = $session->get('cart_id');
        if (!$cartId) {
            $cartId = uniqid('cart_', true);
            $session->set('cart_id', $cartId);
        }
        
        return $cartId;
    }

    #[Route('/library', name: 'app_library')]
    public function library(Request $request, PurchaseRepository $purchaseRepository): Response
    {
        $sessionId = $this->getSessionId($request);
        $purchases = $purchaseRepository->findBy(['sessionId' => $sessionId], ['purchaseDate' => 'DESC']);
        
        return $this->render('library.html.twig', [
            'purchases' => $purchases
        ]);
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(Request $request, CartItemRepository $cartItemRepository, EntityManagerInterface $em, \App\Repository\PurchaseRepository $purchaseRepository): Response
    {
        $sessionId = $this->getSessionId($request);
        $cartItems = $cartItemRepository->findBy(['sessionId' => $sessionId]);
        
        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart');
        }
        
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $existingPurchase = $purchaseRepository->findOneBySessionAndGame($sessionId, $item->getGame()->getId());
            if ($existingPurchase) {
                $existingPurchase->setQuantity($existingPurchase->getQuantity() + $item->getQuantity());
                $existingPurchase->setPurchaseDate(new \DateTimeImmutable()); // Met à jour la date d'achat
            } else {
                $purchase = new Purchase();
                $purchase->setGame($item->getGame());
                $purchase->setSessionId($sessionId);
                $purchase->setPurchaseDate(new \DateTimeImmutable());
                $purchase->setPrice($item->getGame()->getPrice());
                $purchase->setQuantity($item->getQuantity());
                $em->persist($purchase);
            }
            $totalAmount += $item->getGame()->getPrice() * $item->getQuantity();
            $em->remove($item);
        }
        $em->flush();
        $this->addFlash('success', 'Achat terminé avec succès ! Total: ' . number_format($totalAmount, 2) . '€. Vos jeux sont maintenant disponibles dans votre bibliothèque.');
        return $this->redirectToRoute('app_library');
    }
}
