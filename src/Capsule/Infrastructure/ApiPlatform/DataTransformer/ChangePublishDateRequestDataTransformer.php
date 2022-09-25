<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\ApiPlatform\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Capsule\Application\Command\ChangePublishDate;
use App\Capsule\Application\Command\UnpublishCapsule;
use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Domain\Entity\CapsuleId;
use App\Capsule\Domain\Entity\PublishAt;
use App\Capsule\Infrastructure\Http\ChangePublishDateRequest;
use App\Capsule\Infrastructure\Http\UnpublishCapsuleRequest;
use DateTimeImmutable;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Webmozart\Assert\Assert;

class ChangePublishDateRequestDataTransformer implements DataTransformerInterface
{
    /**
     * Public constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function transform($object, string $to, array $context = [])
    {
        /** @var ChangePublishDateRequest $object */
        $object->capsule = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        $this->validator->validate($object);

        return new ChangePublishDate(
            CapsuleId::fromString((string) $object->capsule->getId()),
            PublishAt::createFromDateInterface(new DateTimeImmutable((string) $object->publishAt))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === Capsule::class
            && ($context['input']['class'] ?? null) === ChangePublishDateRequest::class
            && $context[AbstractNormalizer::OBJECT_TO_POPULATE] instanceof Capsule;
    }
}
