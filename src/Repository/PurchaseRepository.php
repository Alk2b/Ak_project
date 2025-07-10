<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function findBySessionId(string $sessionId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.sessionId = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->orderBy('p.purchaseDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
