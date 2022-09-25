<?php

declare(strict_types=1);

namespace App\Capsule\Application\Handler;

use App\Capsule\Application\Command\CreateCapsule;
use App\Capsule\Domain\Entity\{
    Capsule,
    CapsuleContent,
    CapsuleHashedPassword,
    CapsuleId,
    PublishAt
};
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class CreateCapsuleHandler
{
    /**
     * Public constructor.
     *
     * @param CapsuleRepositoryInterface $repository
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(
        private CapsuleRepositoryInterface $repository,
        private PasswordHasherInterface $hasher
    ) {
    }

    public function __invoke(CreateCapsule $command): void
    {
        $hashedPassword = $this
            ->hasher
            ->hash($command->getPassword())
        ;

        $capsule = new Capsule(
            CapsuleId::fromString($command->getId()),
            CapsuleHashedPassword::fromString($hashedPassword),
            CapsuleContent::fromString($command->getContent()),
            PublishAt::createFromDateInterface($command->getPublishAt())
        );

        $this->repository->add($capsule);
    }
}
