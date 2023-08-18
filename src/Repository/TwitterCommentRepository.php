<?php

namespace App\Repository;

use App\Entity\Twitter;
use App\Entity\TwitterComment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TwitterComment>
 *
 * @method TwitterComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method TwitterComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method TwitterComment[]    findAll()
 * @method TwitterComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterCommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TwitterComment::class);
    }

    public function getlistCommentsByTwitter(Twitter $twitter): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.twitter = :twitter')
            ->setParameter('twitter', $twitter)
            ->addOrderBy('c.updatedAt', Criteria::DESC)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countCommentsOfTwitter(Twitter $twitter): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.twitter = :twitter')
            ->setParameter('twitter', $twitter)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
