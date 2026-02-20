<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function getAverageRatingForMovie(int $movieId): ?float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->where('r.movie = :movieId')
            ->andWhere('r.isActive = true')
            ->setParameter('movieId', $movieId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getReviewCountForMovie(int $movieId): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.movie = :movieId')
            ->andWhere('r.isActive = true')
            ->setParameter('movieId', $movieId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopUser(): ?array
    {
        return $this->createQueryBuilder('r')
            ->select('u.email as email, COUNT(r.id) as total')
            ->join('r.user', 'u')
            ->where('r.isActive = true')
            ->groupBy('u.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTopMovie(): ?array
    {
        return $this->createQueryBuilder('r')
            ->select('m.title as title, COUNT(r.id) as total')
            ->join('r.movie', 'm')
            ->where('r.isActive = true')
            ->groupBy('m.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Review[] Returns an array of Review objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Review
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
