<?php

declare(strict_types=1);

namespace App\Capsule\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Embeddable]
final class CapsuleContent implements Stringable
{
    /**
     * Private constructor.
     */
    private function __construct(
        #[
            ORM\Column(type: Types::TEXT)
        ]
        private readonly string $content,
    ) {
    }

    /**
     * Creates new instance from string.
     *
     * @param string $content
     *
     * @return self
     */
    public static function fromString(string $content): self
    {
        return new CapsuleContent($content);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
