<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Capsule;
use Symfony\Component\Serializer\Normalizer\{
    NormalizerAwareInterface,
    NormalizerAwareTrait,
    NormalizerInterface
};

/**
 * Normalized that will erase Capsule::$content from payload if the capsule has not yet been published.
 */
class CapsuleNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * Context key to prevent multiple invoke.
     *
     * @var string
     */
    private const ALREADY_CALLED = 'CAPSULE_NORMALIZER_ALREADY_CALLED';

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof Capsule && !$data->isPublished();
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        assert($object instanceof Capsule);
        $normalized = $this->normalizer->normalize($object, $format, $context);
        $normalized['content'] = null;

        return $normalized;
    }
}
