<?php

declare(strict_types=1);

namespace App\Capsule\Application\Handler;

use App\Capsule\Application\Command\ChangePublishDate;
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;

class ChangePublishDateHandler
{
    /**
     * Public constructor.
     *
     * @param CapsuleRepositoryInterface $repository
     */
    public function __construct(
        private CapsuleRepositoryInterface $repository,
    ) {
    }

    public function __invoke(ChangePublishDate $command): void
    {
        $capsule = $this->repository->findById($command->getId());
        $capsule->updatePublicationDate($command->getPublishAt());

        $this->repository->save($capsule);
    }
}
