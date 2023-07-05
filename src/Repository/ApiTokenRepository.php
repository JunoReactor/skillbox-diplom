<?php

namespace App\Repository;

use App\Entity\ApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ApiTokenRepository extends ServiceEntityRepository
{
    /**
     * ApiTokenRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    /**
     * Find one not expired API token
     *
     * @return ApiToken|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneNotExpired(): ?ApiToken
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.expiresAt <= :val')
            ->setParameter('val', date())
            ->getQuery()
            ->getOneOrNullResult();
    }
}