<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function allMessagesOfDialogue(int $dialogueId, string $userUuid): array
    {
        return $this
            ->createQueryBuilder('m')
            ->where('m.dialogue = :id')
            ->addOrderBy('m.createdAt', Criteria::DESC)
            ->setParameter('id', $dialogueId)
            ->getQuery()
            ->getResult();
    }

    public function numberNotReadMessages(User $user, int $dialogueId = null): int
    {
        $query = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->leftJoin('m.receiver', 'receiver')
            ->where('m.status = :status')
            ->andWhere('receiver = :user')
            ->setParameter('user', $user)
            ->setParameter('status', false);

        if ($dialogueId) {
            $query->andWhere('m.dialogue = :id')
                ->setParameter('id', $dialogueId);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function existsSenderOfMessageByUuid(string $uuid, UserInterface $user): bool
    {
        return null !== $this->findOneBy(['uuid' => $uuid, 'sender' => $user]);
    }
}
