<?php

declare(strict_types=1);

namespace App\Capsule\Domain\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Capsule\Infrastructure\Http\CapsuleResponse;
use App\Capsule\Infrastructure\Http\ChangePublishDateRequest;
use App\Capsule\Infrastructure\Http\CreateCapsuleRequest;
use App\Capsule\Infrastructure\Http\UnpublishCapsuleRequest;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base application object that contains all user provided content.
 */
#[
    ORM\Entity,
    ORM\Table(
        name: 'capsules',
        schema: 'app'
    ),
    ApiResource(
        collectionOperations: [
            'post' => [
                'input' => CreateCapsuleRequest::class,
                'output' => CapsuleResponse::class,
                'normalization_context' => [
                    'groups' => [
                        self::READ_GROUP,
                    ]
                ],
                'denormalization_context' => [
                    'groups' => [
                        self::WRITE_GROUP,
                    ]
                ],
            ]
        ],
        itemOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => [
                        self::READ_GROUP,
                    ]
                ],
            ],
            'change-publication-date' => [
                'path' => '/capsules/{id}',
                'method' => Request::METHOD_PUT,
                'input' => ChangePublishDateRequest::class,
                'read' => true,
            ],
            'unpublish' => [
                'path' => '/capsules/{id}/unpublish',
                'input' => UnpublishCapsuleRequest::class,
                'method' =>  Request::METHOD_PUT,
                'read' => true,
            ],
        ],
        output: CapsuleResponse::class,
    ),
]
class Capsule
{
    /**
     * Serialization groups.
     *
     * @var string
     */
    public const READ_GROUP = 'Capsule:read';
    public const WRITE_GROUP = 'Capsule:write';

    /**
     * Capsule statuses.
     *
     * @var string
     */
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PUBLISHED = 'published';

    /**
     * Capsule text content provided by client.
     *
     * Mutable only for POST operation.
     * Note that this property will be erased before publication
     * by {@link CapsuleNormalizer}
     */
    #[
        ORM\Embedded,
        Assert\Valid,
    ]
    private CapsuleContent $content;

    /**
     * Current capsule entry status.
     */
    #[
        ORM\Column(
            type: Types::TEXT,
        ),
        Groups([
            self::READ_GROUP,
        ]),
    ]
    private string $status = self::STATUS_QUEUED;

    public function __construct(
        #[
            ORM\Id,
            ORM\Embedded,
            Assert\Valid,
        ]
        private CapsuleId $id,
        #[
            ORM\Embedded,
            Assert\Valid,
        ]
        private CapsuleHashedPassword $password,
        CapsuleContent $content,
        #[
            ORM\Embedded,
            Assert\Valid,
        ]
        private PublishAt $publishAt,
    ) {
        $this->content = $content;
    }

    /**
     * Gets content.
     *
     * @return CapsuleContent
     */
    public function getContent(): CapsuleContent
    {
        return $this->content;
    }

    /**
     * Gets password.
     *
     * @return CapsuleHashedPassword
     */
    public function getPassword(): CapsuleHashedPassword
    {
        return $this->password;
    }

    /**
     * Gets publishAt.
     *
     * @return PublishAt
     */
    public function getPublishAt(): PublishAt
    {
        return $this->publishAt;
    }

    /**
     * Gets status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Sets status.
     *
     * @param string $status
     *
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Checks if object is published.
     *
     * @return bool
     */
    #[
        Groups([
            self::READ_GROUP
        ])
    ]
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function unpublish(): void
    {
        $this->publishAt = PublishAt::createFromDateInterface(new DateTimeImmutable('2555-01-01'));
    }

    public function updatePublicationDate(PublishAt $publishAt): void
    {
        $this->publishAt = $publishAt;
    }

    /**
     * @return CapsuleId
     */
    public function getId(): CapsuleId
    {
        return $this->id;
    }
}
