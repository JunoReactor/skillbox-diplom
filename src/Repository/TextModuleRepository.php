<?php

namespace App\Repository;

use App\Entity\TextModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TextModule>
 *
 * @method TextModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextModule[]    findAll()
 * @method TextModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextModule::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TextModule $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getCount(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(TextModule $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
