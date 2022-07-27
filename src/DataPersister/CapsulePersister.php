<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\{
    ContextAwareDataPersisterInterface,
    ResumableDataPersisterInterface
};
use App\Entity\Capsule;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * Class CapsulePersister
 */
class CapsulePersister implements ContextAwareDataPersisterInterface, ResumableDataPersisterInterface
{
    /**
     * Dependency injection.
     *
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(private PasswordHasherInterface $hasher)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Capsule;
    }

    /**
     * {@inheritDoc}
     */
    public function persist($data, array $context = [])
    {
        assert($data instanceof Capsule);

        if (($context['collection_operation_name'] ?? null) === 'post') {
            $hashedPassword = $this
                ->hasher
                ->hash($data->getRawPassword())
            ;
            $data->setPassword($hashedPassword);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($data, array $context = [])
    {
    }

    /**
     * {@inheritDoc}
     */
    public function resumable(array $context = []): bool
    {
        return true;
    }
}
