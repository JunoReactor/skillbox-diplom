Копировать
<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class ArticleRepository extends ServiceEntityRepository
{
    /**
     * ArticleRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Find latest published articles with comments and tags
     *
     * @return Article[]
     */
    public function findLatestPublished(): array
    {
        return $this->published($this->latest())
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->leftJoin('a.tags', 't')
            ->addSelect('t')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get count of all articles
     *
     * @return int
     */
    public function getCountFull(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get count of articles for current month
     *
     * @return int
     */
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
     * Find latest articles
     *
     * @return Article[]
     */
    public function findLatest(): array
    {
        return $this->latest()
            ->getQuery()
            ->getResult();
    }

    /**
     * Find published articles
     *
     * @return Article[]
     */
    public function findPublished(): array
    {
        return $this->published()
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all articles with search query
     *
     * @param string|null $search
     * @param bool $withSoftDeletes
     * @return QueryBuilder
     */
    public function findAllWithSearchQuery(?string $search, bool $withSoftDeletes = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->innerJoin('c.author', 'a')
            ->addSelect('a');

        if ($search) {
            $qb
                ->andWhere('c.body LIKE :search OR c.title LIKE :search OR a.firstName LIKE :search ')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($withSoftDeletes) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        return $qb
            ->orderBy('c.publishedAt', 'DESC');
    }

    /**
     * Set published filter to query builder
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function published(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)->andWhere('a.publishedAt IS NOT NULL');
    }

    /**
     * Set latest order to query builder
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    public function latest(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)->orderBy('a.publishedAt', 'DESC');
    }

    /**
     * Get or create query builder object for repository
     *
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?? $this->createQueryBuilder('a');
    }
}