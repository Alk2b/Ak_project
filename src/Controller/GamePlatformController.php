<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\CartItem;
use App\Repository\GameRepository;
use App\Repository\CartItemRepository;
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
        // Récupérer les 3 premiers jeux pour la page d'accueil
        $featuredGames = $gameRepository->findBy([], null, 3);
        
        return $this->render('index.html.twig', [
            'featured_games' => $featuredGames
        ]);
    }

    #[Route('/shop', name: 'app_shop')]
    public function shop(GameRepository $gameRepository): Response
    {
        // Récupérer tous les jeux
        $games = $gameRepository->findAll();
        
        return $this->render('shop.html.twig', [
            'games' => $games
        ]);
    }

    #[Route('/cart', name: 'app_cart')]
    public function cart(Request $request, CartItemRepository $cartItemRepository): Response
    {
        $sessionId = $this->getSessionId($request);
        
        // Debug: afficher l'ID de session
        dump('Session ID: ' . $sessionId);
        
        // Récupérer les items du panier pour cette session
        $cartItems = $cartItemRepository->findBy(['sessionId' => $sessionId]);
        
        // Debug: afficher le nombre d'items
        dump('Nombre d\'items dans le panier: ' . count($cartItems));
        
        // Calculer le total
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
        
        // Debug
        dump('Add to cart - Session ID: ' . $sessionId);
        
        // Vérifier si l'item existe déjà dans le panier
        $existingItem = $cartItemRepository->findOneBy([
            'game' => $game,
            'sessionId' => $sessionId
        ]);
        
        if ($existingItem) {
            // Augmenter la quantité
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
            dump('Item existant trouvé, nouvelle quantité: ' . $existingItem->getQuantity());
        } else {
            // Créer un nouvel item
            $cartItem = new CartItem();
            $cartItem->setGame($game);
            $cartItem->setSessionId($sessionId);
            $cartItem->setQuantity(1);
            $em->persist($cartItem);
            dump('Nouvel item créé');
        }
        
        $em->flush();
        
        $this->addFlash('success', 'Le jeu "' . $game->getTitle() . '" a été ajouté au panier !');
        
        return $this->redirectToRoute('app_cart');
    }
    
    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(CartItem $cartItem, Request $request, EntityManagerInterface $em): Response
    {
        $sessionId = $this->getSessionId($request);
        
        // Debug
        dump('Remove from cart - Session ID: ' . $sessionId);
        dump('CartItem Session ID: ' . $cartItem->getSessionId());
        
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
        
        // Debug
        dump('Decrease from cart - Session ID: ' . $sessionId);
        dump('CartItem Session ID: ' . $cartItem->getSessionId());
        
        if ($cartItem->getSessionId() !== $sessionId) {
            $this->addFlash('error', 'Erreur: cet item ne vous appartient pas.');
            return $this->redirectToRoute('app_cart');
        }
        
        if ($cartItem->getQuantity() > 1) {
            // Diminuer la quantité
            $cartItem->setQuantity($cartItem->getQuantity() - 1);
            $em->flush();
            $this->addFlash('success', 'Quantité mise à jour.');
        } else {
            // Supprimer l'item si la quantité est 1
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
        
        // Créer un ID de panier stable
        $cartId = $session->get('cart_id');
        if (!$cartId) {
            $cartId = uniqid('cart_', true);
            $session->set('cart_id', $cartId);
        }
        
        return $cartId;
    }

    #[Route('/library', name: 'app_library')]
    public function library(): Response
    {
        // Pour l'instant, bibliothèque statique (sera améliorée plus tard)
        return $this->render('library.html.twig');
    }
}
