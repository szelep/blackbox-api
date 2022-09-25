<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\ApiPlatform\DataPersister;

use App\Capsule\Application\Command\ChangePublishDate;
use App\Capsule\Application\Command\CreateCapsule;
use App\Capsule\Application\Command\UnpublishCapsule;
use App\Capsule\Application\Handler\ChangePublishDateHandler;
use App\Capsule\Application\Handler\CreateCapsuleHandler;
use App\Capsule\Application\Handler\UnpublishCapsuleHandler;
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Capsule\Infrastructure\Http\ChangePublishDateRequest;
use RuntimeException;

/**
 * Class CapsulePersister.
 */
class CapsulePersister implements ContextAwareDataPersisterInterface
{
    /**
     * Dependency injection.
     *
     * @param CreateCapsuleHandler $createCapsuleHandler
     * @param CapsuleRepositoryInterface $repository
     */
    public function __construct(
        private CreateCapsuleHandler $createCapsuleHandler,
        private UnpublishCapsuleHandler $unpublishCapsuleHandler,
        private ChangePublishDateHandler $changePublishDateHandler,
        private CapsuleRepositoryInterface $repository,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof CreateCapsule
            || $data instanceof UnpublishCapsule
            || $data instanceof ChangePublishDate;
    }

    /**
     * {@inheritDoc}
     */
    public function persist($data, array $context = [])
    {
        match (true) {
            $data instanceof CreateCapsule => $this->createCapsuleHandler->__invoke($data),
            $data instanceof UnpublishCapsule => $this->unpublishCapsuleHandler->__invoke($data),
            $data instanceof ChangePublishDate => $this->changePublishDateHandler->__invoke($data),
        };

        return $this->repository->getById((string) $data->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function remove($data, array $context = [])
    {
    }
}
