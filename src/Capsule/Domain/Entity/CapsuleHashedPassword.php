<?php

declare(strict_types=1);

namespace App\Capsule\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Embeddable]
final class CapsuleHashedPassword implements Stringable
{
    /**
     * Private constructor.
     */
    private function __construct(
        #[
            ORM\Column(type: Types::TEXT)
        ]
        private readonly string $password,
    ) {
    }

    /**
     * Creates new instance from hashed password string.
     *
     * @param string $hashedPassword
     *
     * @return self
     */
    public static function fromString(string $hashedPassword): self
    {
        return new CapsuleHashedPassword($hashedPassword);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->password;
    }
}
