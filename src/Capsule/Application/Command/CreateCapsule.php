<?php

declare(strict_types=1);

namespace App\Capsule\Application\Command;

use DateTimeInterface;

final class CreateCapsule
{
    public function __construct(
        private readonly string $id,
        private readonly string $content,
        private readonly string $password,
        private readonly DateTimeInterface $publishAt,
    ) {
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return DateTimeInterface
     */
    public function getPublishAt(): DateTimeInterface
    {
        return $this->publishAt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
