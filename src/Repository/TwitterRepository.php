<?php

namespace App\Repository;

use App\Entity\Twitter;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Twitter>
 *
 * @method Twitter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Twitter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Twitter[]    findAll()
 * @method Twitter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TwitterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Twitter::class);
    }

    // All twitters by user
    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->addOrderBy('t.createdAt', Criteria::DESC)
            ->getQuery()
            ->getResult()
        ;
    }
}
