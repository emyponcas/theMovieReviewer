<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    #metodo para encontrar por cantidad de votos
    public function findAllOrderedByVoteCount(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.vote_count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchAndFilter(?string $search, ?string $language): array
    {
        $qb = $this->createQueryBuilder('m');

        $qb->andWhere('m.isActive = true');

        if ($search) {
            $qb->andWhere('LOWER(m.title) LIKE LOWER(:search)')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($language) {
            $qb->andWhere('m.original_language = :language')
                ->setParameter('language', $language);
        }

        $qb->orderBy('m.title', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findAvailableLanguages(): array
    {
        return $this->createQueryBuilder('m')
            ->select('DISTINCT m.original_language')
            ->where('m.original_language IS NOT NULL')
            ->andWhere('m.isActive = true')
            ->orderBy('m.original_language', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }


    //    /**
    //     * @return Movie[] Returns an array of Movie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Movie
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
