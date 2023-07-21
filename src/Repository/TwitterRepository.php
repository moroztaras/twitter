<?php

namespace App\Repository;

use App\Entity\Twitter;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    /**
     * @return \Traversable&\Countable
     */
    public function getPageByUserId(int $id, int $offset, int $limit)
    {
        $query = $this->_em->createQuery('SELECT t FROM App\Entity\Twitter t WHERE t.user = :id ORDER BY t.createdAt DESC')
            ->setParameter('id', $id)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query, false);
    }

    public function countTwittersOfUser(User $user): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
