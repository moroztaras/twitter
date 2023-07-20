<?php

namespace App\Repository;

use App\Entity\Friend;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Friend>
 *
 * @method Friend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friend[]    findAll()
 * @method Friend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friend::class);
    }

    public function findOneByUsers(User $user, User $friend): Friend|null
    {
        return $this->findOneBy([
            'user' => $user->getId(), 'friend' => $friend->getId(),
        ]);
    }

    public function countFollowingsOfOneUser($user): int
    {
        return $this->createQueryBuilder('fr')
            ->select('COUNT(fr.id)')
            ->where('fr.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function allFollowingsOfOneUser(User $user): array
    {
        return $this->createQueryBuilder('fr')
            ->where('fr.user = :user')
            ->setParameter('user', $user)
            ->orderBy('fr.createdAt', Criteria::DESC)
            ->getQuery()
            ->getResult();
    }
}
