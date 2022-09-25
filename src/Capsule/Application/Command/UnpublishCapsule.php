<?php

declare(strict_types=1);

namespace App\Capsule\Application\Command;

final class UnpublishCapsule
{
    public function __construct(
        private readonly string $id,
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
