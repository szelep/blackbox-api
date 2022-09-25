<?php

declare(strict_types=1);

namespace App\Capsule\Application\Command;

use App\Capsule\Domain\Entity\{
    CapsuleId,
    PublishAt
};

final class ChangePublishDate
{
    public function __construct(
        private readonly CapsuleId $id,
        private readonly PublishAt $publishAt
    ) {
    }

    /**
     * @return CapsuleId
     */
    public function getId(): CapsuleId
    {
        return $this->id;
    }

    /**
     * @return PublishAt
     */
    public function getPublishAt(): PublishAt
    {
        return $this->publishAt;
    }
}
