<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

     /**
      * @return Article[] Returns an array of Article objects
      */
    public function findLatestPublished()
    {
        return $this->published($this->latest())
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getCountFull(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getCountMonth(): int
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('MONTH(u.createdAt) = :month')
            ->setParameter('month', $now->format('m'))
            ->getQuery()
            ->getSingleScalarResult();
    }
    
     /**
      * @return Article[] Returns an array of Article objects
      */
    public function findLatest()
    {
        return $this->latest()
            ->getQuery()
            ->getResult()
        ;
    }
    
     /**
      * @return Article[] Returns an array of Article objects
      */
    public function findPublished()
    {
        return $this->published()            
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false)
    {
        $qb = $this->createQueryBuilder('c');
        # При введенном значении в поле фильтр, фильтрация должна осуществляться по названию, содержимому или автору статьи.
        
        $qb
            ->innerJoin('c.author', 'a')
            ->addSelect('a')
        ;
        
        if ($search) {
            $qb
                ->andWhere('c.body LIKE :search OR c.title LIKE :search OR a.firstName LIKE :search ')
                ->setParameter('search', '%' . $search . '%')
            ;
        }
        
        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }
        //dd('111');
        return $qb
            ->orderBy('c.publishedAt', 'DESC')
        ;
    }
    
    private function published(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)->andWhere('a.publishedAt IS NOT NULL');
    }
    
    public function latest(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)->orderBy('a.publishedAt', 'DESC');
    }

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('a');
    }
}
