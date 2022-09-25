<?php

declare(strict_types=1);

namespace App\Capsule\Domain\Repository;

use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Domain\Entity\CapsuleId;

interface CapsuleRepositoryInterface
{
    /**
     * Finds capsules with 'queued' status and with publication date before current date.
     *
     * @return array
     */
    public function findAllForPublication(): array;

    /**
     * Adds new object to database.
     *
     * @param Capsule $capsule
     *
     * @return void
     */
    public function add(Capsule $capsule): void;

    /**
     * @param CapsuleId $capsuleId
     *
     * @return Capsule|null
     */
    public function findById(CapsuleId $capsuleId): ?Capsule;

    /**
     * Gets Capsule by identifier.
     *
     * @param string $id
     *
     * @return Capsule
     */
    public function getById(string $id): Capsule;

    public function save(Capsule $capsule): void;
}
