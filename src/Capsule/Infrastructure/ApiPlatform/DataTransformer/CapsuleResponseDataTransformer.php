<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\ApiPlatform\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Infrastructure\Http\CapsuleResponse;

class CapsuleResponseDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        /** @var Capsule $object */
        return new CapsuleResponse(
            (string) $object->getId(),
            (string) $object->getContent(),
            $object->getPublishAt()->toImmutable(),
            $object->isPublished()
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Capsule && $to === CapsuleResponse::class;
    }
}
