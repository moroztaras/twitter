<?php

namespace App\Repository;

use App\Entity\Dialogue;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dialogue>
 *
 * @method Dialogue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dialogue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dialogue[]    findAll()
 * @method Dialogue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DialogueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dialogue::class);
    }

    public function dialoguesOfUser(User $user): array
    {
        return $this
            ->createQueryBuilder('d')
            ->select('d')
            ->where('d.receiver = :user OR d.creator = :user')
            ->addOrderBy('d.updatedAt', Criteria::DESC)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
