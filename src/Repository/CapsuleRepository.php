<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Capsule;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Capsule>
 *
 * @method Capsule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Capsule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Capsule[]    findAll()
 * @method Capsule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null
 *
 * @SuppressWarnings(PHPMD.MemberPrimaryPrefix)
 */
class CapsuleRepository extends ServiceEntityRepository
{
    /**
     * Register repository.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Capsule::class);
    }

    /**
     * Finds capsules with 'queued' status and with publication date before current date.
     *
     * @return array
     */
    public function findAllForPublication(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.publishAt < :now')
            ->setParameter('now', new DateTimeImmutable())
            ->andWhere('c.status = :queuedStatus')
            ->setParameter('queuedStatus', Capsule::STATUS_QUEUED)
            ->orderBy('c.publishAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
