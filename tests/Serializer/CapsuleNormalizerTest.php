<?php

declare(strict_types=1);

namespace App\Tests\Serializer;

use App\Entity\Capsule;
use App\Serializer\CapsuleNormalizer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class CapsuleNormalizerTest
 */
class CapsuleNormalizerTest extends TestCase
{
    /**
     * @dataProvider supportsNormalizationProvider
     *
     * @param mixed $data
     * @param array $context
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testSupportsNormalization(mixed $data, array $context, bool $expectedResult): void
    {
        $normalizer = new CapsuleNormalizer();

        $supports = $normalizer->supportsNormalization(
            data: $data,
            context: $context
        );

        $this->assertSame($expectedResult, $supports);
    }

    /**
     * @return array[]
     */
    public function supportsNormalizationProvider(): array
    {
        return [
            'already called' => [
                'data' => new Capsule(),
                'context' => [
                    'CAPSULE_NORMALIZER_ALREADY_CALLED' => true,
                ],
                'expected_result' => false,
            ],
            'is published' => [
                'data' => (new Capsule())->setStatus(Capsule::STATUS_PUBLISHED),
                'context' => [],
                'expected_result' => false,
            ],
            'not instance of Capsule' => [
                'data' => new stdClass(),
                'context' => [],
                'expected_result' => false,
            ],
            'valid object' => [
                'data' => new Capsule(),
                'context' => [],
                'expected_result' => true,
            ]
        ];
    }

    /**
     * @return void
     */
    public function testContentErasedUsed(): void
    {
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        $normalizerMock
            ->method('normalize')
            ->willReturn([
                'content' => 'this should be erased',
                'not_content' => 'this should NOT be erased',
            ])
        ;
        $normalizer = new CapsuleNormalizer();
        $normalizer->setNormalizer($normalizerMock);

        $result = $normalizer->normalize(new stdClass());

        $this->assertSame(
            [
                'content' => null,
                'not_content' => 'this should NOT be erased',
            ],
            $result
        );
    }
}
