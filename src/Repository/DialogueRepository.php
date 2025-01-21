<?php

namespace App\Repository;

use App\Entity\Dialogue;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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
            ->where('d.receiver = :user OR d.creator = :user')
            ->addOrderBy('d.updatedAt', Criteria::DESC)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findOneDialogue(User $user, User $receiver): ?Dialogue
    {
        return $this
            ->createQueryBuilder('d')
            ->select('d')
            ->where('d.creator = :user OR d.receiver = :receiver')
            ->orWhere('d.creator = :receiver OR d.receiver = :user')
            ->setParameter('user', $user)
            ->setParameter('receiver', $receiver)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsCreatorDialogueByUuid(string $uuid, UserInterface $user): bool
    {
        return null !== $this->findOneBy(['uuid' => $uuid, 'creator' => $user]);
    }

    public function findOneByUuid(string $uuid): ?Dialogue
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }
}
