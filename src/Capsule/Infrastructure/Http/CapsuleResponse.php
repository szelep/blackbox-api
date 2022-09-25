<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\Http;

use App\Capsule\Domain\Entity\Capsule;
use Symfony\Component\Serializer\Annotation\Groups;

class CapsuleResponse
{
    public function __construct(
        #[Groups([Capsule::READ_GROUP])]
        public string $id,
        #[Groups([Capsule::READ_GROUP])]
        public string $content,
        #[Groups([Capsule::READ_GROUP])]
        public \DateTimeInterface $publishAt,
        #[Groups([Capsule::READ_GROUP])]
        public bool $published,
    ) {
    }
}
