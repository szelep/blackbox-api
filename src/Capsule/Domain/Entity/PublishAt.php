<?php

declare(strict_types=1);

namespace App\Capsule\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Embeddable]
final class PublishAt implements Stringable
{
    private function __construct(
        #[
            ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)
        ]
        private DateTimeInterface $publishAt
    ) {
    }

    public static function createFromDateInterface(DateTimeInterface $dateTime): self
    {
        return new PublishAt($dateTime);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->publishAt->format(DateTimeInterface::RFC3339);
    }

    public function toImmutable(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromInterface($this->publishAt);
    }
}
