<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\ApiPlatform\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Capsule\Application\Command\CreateCapsule;
use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Infrastructure\Http\CreateCapsuleRequest;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class CreateCapsuleRequestDataTransformer implements DataTransformerInterface
{
    /**
     * Public constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function transform($object, string $to, array $context = [])
    {
        /** @var CreateCapsuleRequest $object */
        $this->validator->validate($object);

        return new CreateCapsule(
            Uuid::v4()->toRfc4122(),
            $object->content,
            $object->password,
            new DateTimeImmutable($object->publishAt)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === Capsule::class && ($context['input']['class'] ?? null) === CreateCapsuleRequest::class;
    }
}
