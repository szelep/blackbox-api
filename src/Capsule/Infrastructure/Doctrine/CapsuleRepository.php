<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\Doctrine;

use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Domain\Entity\CapsuleId;
use App\Capsule\Domain\Entity\PublishAt;
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @extends ServiceEntityRepository<Capsule>
 *
 * @method Capsule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Capsule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Capsule[]    findAll()
 * @method Capsule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @SuppressWarnings(PHPMD.MemberPrimaryPrefix)
 */
class CapsuleRepository extends ServiceEntityRepository implements CapsuleRepositoryInterface
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
            ->andWhere('c.publishAt.publishAt < :now')
            ->setParameter('now', new DateTimeImmutable())
            ->andWhere('c.status = :queuedStatus')
            ->setParameter('queuedStatus', Capsule::STATUS_QUEUED)
            ->orderBy('c.publishAt.publishAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function add(Capsule $capsule): void
    {
        $this->getEntityManager()->persist($capsule);
        $this->getEntityManager()->flush();
    }

    public function findById(CapsuleId $capsuleId): ?Capsule
    {
        return $this->findOneBy(['id.id' => (string) $capsuleId]);
    }

    public function getById(string $id): Capsule
    {
        return $this->findById(CapsuleId::fromString($id));
    }

    public function save(Capsule $capsule): void
    {
        $this->getEntityManager()->flush();
    }
}
