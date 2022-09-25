<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\ApiPlatform\DataProvider;

use App\Capsule\Domain\Entity\{
    Capsule,
    CapsuleId
};
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;
use ApiPlatform\Core\DataProvider\{
    ItemDataProviderInterface,
    RestrictedDataProviderInterface
};

class CapsuleDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private CapsuleRepositoryInterface $repository)
    {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->repository->findById(CapsuleId::fromString($id));
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Capsule::class;
    }
}
