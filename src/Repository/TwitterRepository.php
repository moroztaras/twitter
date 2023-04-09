<?php

namespace App\Repository;

use App\Entity\Twitter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function save(Twitter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Twitter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
