<?php

declare(strict_types=1);

namespace App\Capsule\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

/**
 * Capsule identifier object.
 */
#[ORM\Embeddable]
final class CapsuleId implements Stringable
{
    /**
     * Private constructor.
     *
     * @param Uuid $id
     */
    private function __construct(
        #[
           ORM\Id,
           ORM\Column(type: 'uuid', unique: true, nullable: false)
       ]
        private readonly Uuid $id,
    ) {
    }

    /**
     * Generates new instnance of CapsuleId.
     *
     * @return self
     */
    public static function generate(): self
    {
        return new self(Uuid::v4());
    }

    /**
     * Creates new instance from uuid string.
     *
     * @param string $uuid
     *
     * @return self
     */
    public static function fromString(string $uuid): self
    {
        Assert::uuid($uuid);

        return new CapsuleId(Uuid::fromString($uuid));
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->id->toRfc4122();
    }
}
