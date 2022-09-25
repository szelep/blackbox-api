<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\ApiPlatform\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Capsule\Application\Command\UnpublishCapsule;
use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Infrastructure\Http\UnpublishCapsuleRequest;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UnpublishCapsuleRequestDataTransformer implements DataTransformerInterface
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
        /** @var UnpublishCapsuleRequest $object */
        $object->capsule = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        $this->validator->validate($object);

        return new UnpublishCapsule((string) $object->capsule->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === Capsule::class
            && ($context['input']['class'] ?? null) === UnpublishCapsuleRequest::class
            && $context[AbstractNormalizer::OBJECT_TO_POPULATE] instanceof Capsule;
    }
}
