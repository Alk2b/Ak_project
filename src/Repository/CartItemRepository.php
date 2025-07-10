<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function findBySessionId(string $sessionId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.sessionId = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery()
            ->getResult();
    }

    public function findBySessionAndGame(string $sessionId, int $gameId): ?CartItem
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.sessionId = :sessionId')
            ->andWhere('c.game = :gameId')
            ->setParameter('sessionId', $sessionId)
            ->setParameter('gameId', $gameId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
