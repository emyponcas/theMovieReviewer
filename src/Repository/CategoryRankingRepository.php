<?php

namespace App\Repository;

use App\Entity\CategoryRanking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryRanking::class);
    }

    public function getLeaderboard(int $categoryId): array
    {
        return $this->createQueryBuilder('cr')
            ->select('
                m.id,
                m.title,
                m.poster_path,
                AVG(cr.position) as avg_position,
                COUNT(cr.id) as total_votes
            ')
            ->join('cr.movie', 'm')
            ->where('cr.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->groupBy('m.id')
            ->orderBy('avg_position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalParticipants(int $categoryId): int
    {
        return $this->createQueryBuilder('cr')
            ->select('COUNT(DISTINCT cr.user)')
            ->where('cr.category = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
