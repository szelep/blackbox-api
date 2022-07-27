<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UnpublishAction;
use App\DataPersister\CapsulePersister;
use App\Repository\CapsuleRepository;
use App\Serializer\CapsuleNormalizer;
use App\Validator\ValidModificationPassword;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\{
    Groups,
    Ignore
};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base application object that contains all user provided content.
 */
#[
    ORM\Entity(repositoryClass: CapsuleRepository::class),
    ORM\Table(
        name: 'capsules',
        schema: 'app'
    ),
    ApiResource(
        collectionOperations: [
            'post' => [
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
                'denormalization_context' => [
                    'groups' => [
                        self::WRITE_GROUP,
                    ]
                ],
                'validation_groups' => [
                    self::WRITE_GROUP,
                ]
            ]
        ],
        itemOperations: [
            'get' => [
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
            ],
            'put' => [
                'normalization_context' => [
                    'groups' => [
                        'Identifier:read',
                        self::READ_GROUP,
                    ]
                ],
                'denormalization_context' => [
                    'groups' => [
                        self::UPDATE_GROUP,
                    ]
                ],
                'validation_groups' => [
                    self::UPDATE_GROUP,
                ]
            ],
            'unpublish' => [
                'path' => '/capsules/{id}/unpublish',
                'controller' => UnpublishAction::class,
                'method' =>  Request::METHOD_PUT,
                'read' => true,
                'denormalization_context' => [
                    'groups' => [
                        self::UPDATE_GROUP,
                    ]
                ],
            ],
        ],
    ),
    Assert\Expression(
        'this.getStatus() !== published_status',
        message: 'Modification is not available for published capsules.',
        values: [
            'published_status' => self::STATUS_PUBLISHED
        ],
        groups: [self::UPDATE_GROUP],
    ),
]
class Capsule
{
    use IdentifierTrait;

    /**
     * Serialization groups.
     *
     * @var string
     */
    public const READ_GROUP = 'Capsule:read';
    public const WRITE_GROUP = 'Capsule:write';
    public const UPDATE_GROUP = 'Capsule:update';
    public const UNPUBLISH_GROUP = 'Capsule:unpublish';

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
        ORM\Column(type: Types::TEXT),
        Assert\NotBlank(groups: [self::WRITE_GROUP]),
        Assert\Length(
            max: 10000,
            groups: [self::WRITE_GROUP]
        ),
        Groups([
            self::WRITE_GROUP,
            self::READ_GROUP
        ]),
    ]
    private ?string $content = null;

    /**
     * Hashed capsule modification password.
     *
     * @see CapsulePersister
     */
    #[
        ORM\Column(type: Types::TEXT),
        Ignore,
    ]
    private ?string $password = null;

    /**
     * Raw capsule password provided by user.
     *
     * Virtual property used only for set hashed modification password.
     * Mutable only for POST operation.
     */
    #[
        Assert\NotBlank(groups: [self::WRITE_GROUP]),
        Groups([
            self::WRITE_GROUP
        ])
    ]
    private ?string $rawPassword = null;

    /**
     * Password for modification operation.
     *
     * It will be used to PUT authorization.
     */
    #[
        Assert\Sequentially(
            constraints: [
                new Assert\NotBlank(groups: [
                    self::UPDATE_GROUP,
                    self::UNPUBLISH_GROUP,
                ]),
                new ValidModificationPassword(groups: [
                    self::UPDATE_GROUP,
                    self::UNPUBLISH_GROUP,
                ])
            ]
        ),
        Groups([
            self::UPDATE_GROUP,
            self::UNPUBLISH_GROUP,
        ])
    ]
    private ?string $modificationPassword = null;

    /**
     * Capsule content will be readable after this publication date.
     */
    #[
        ORM\Column(type: Types::DATETIMETZ_IMMUTABLE),
        Assert\GreaterThan(
            'now',
            groups: [
                self::WRITE_GROUP,
                self::UPDATE_GROUP
            ]
        ),
        Assert\NotBlank(
            groups: [
                self::WRITE_GROUP,
                self::UPDATE_GROUP
            ]
        ),
        Groups([
            self::WRITE_GROUP,
            self::READ_GROUP,
            self::UPDATE_GROUP
        ])
    ]
    private ?DateTimeImmutable $publishAt = null;

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

    /**
     * Gets content.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets content.
     *
     * Only some html tags are allowed.
     *
     * @param string $content
     *
     * @return self
     */
    public function setContent(string $content): self
    {
        $allowedHtmlTags = [
            'p',
            'ul',
            'li',
            'strong',
            'i',
            'figure',
            'tbody',
            'tr',
            'td',
            'blockquote',
            'table',
            'h2',
            'h3',
            'h4',
        ];
        $this->content = strip_tags($content, $allowedHtmlTags);

        return $this;
    }

    /**
     * Gets password.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets password.
     *
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets publishAt.
     *
     * @return DateTimeImmutable|null
     */
    public function getPublishAt(): ?DateTimeImmutable
    {
        return $this->publishAt;
    }

    /**
     * Sets publishAt.
     *
     * @param DateTimeImmutable $publishAt
     *
     * @return self
     */
    public function setPublishAt(DateTimeImmutable $publishAt): self
    {
        $this->publishAt = $publishAt;

        return $this;
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

    /**
     * Gets rawPassword.
     *
     * @return string|null
     */
    public function getRawPassword(): ?string
    {
        return $this->rawPassword;
    }

    /**
     * Sets rawPassword.
     *
     * @param string|null $rawPassword
     *
     * @return self
     */
    public function setRawPassword(?string $rawPassword): self
    {
        $this->rawPassword = $rawPassword;

        return $this;
    }

    /**
     * Gets modificationPassword.
     *
     * @return string|null
     */
    public function getModificationPassword(): ?string
    {
        return $this->modificationPassword;
    }

    /**
     * Sets modificationPassword.
     *
     * @param string|null $modificationPassword
     *
     * @return self
     */
    public function setModificationPassword(?string $modificationPassword): self
    {
        $this->modificationPassword = $modificationPassword;

        return $this;
    }
}
