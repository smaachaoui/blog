<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function search(?string $query, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($query) {
            $qb
                ->andWhere('p.title LIKE :q OR p.bookAuthor LIKE :q OR p.content LIKE :q')
                ->setParameter('q', '%' . $query . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function countSearch(?string $query): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');

        if ($query) {
            $qb
                ->andWhere('p.title LIKE :q OR p.bookAuthor LIKE :q OR p.content LIKE :q')
                ->setParameter('q', '%' . $query . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function searchInCategory(int $categoryId, string $q, int $limit, int $offset): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :categoryId')
            ->andWhere('p.title LIKE :q OR p.bookAuthor LIKE :q OR p.content LIKE :q')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('q', '%'.$q.'%')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function countSearchInCategory(int $categoryId, string $q): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.category = :categoryId')
            ->andWhere('p.title LIKE :q OR p.bookAuthor LIKE :q OR p.content LIKE :q')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('q', '%'.$q.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }




}
