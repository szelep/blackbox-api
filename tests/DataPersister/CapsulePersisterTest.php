<?php

declare(strict_types=1);

namespace App\Tests\DataPersister;

use App\DataPersister\CapsulePersister;
use App\Entity\Capsule;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * Class CapsulePersisterTest.
 */
class CapsulePersisterTest extends TestCase
{
    /**
     * @dataProvider supportsProvider
     *
     * @param object $object
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testSupportsClass(object $object, bool $expectedResult): void
    {
        $persister = new CapsulePersister(
            $this->createMock(PasswordHasherInterface::class)
        );

        $result = $persister->supports($object);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array[]
     */
    public function supportsProvider(): array
    {
        return [
            'unsupported class std' => [
                new stdClass(),
                false,
            ],
            'unsupported class' => [
                new class {
                },
                false
            ],
            'supported class' => [
                new Capsule(),
                true
            ],
        ];
    }

    /**
     * @return void
     */
    public function testPersisterIsResumable(): void
    {
        $persister = new CapsulePersister(
            $this->createMock(PasswordHasherInterface::class)
        );

        $this->assertTrue($persister->resumable());
    }

    /**
     * @testdox password should not be invoked on some request methods
     *
     * @dataProvider hasherNotInvokedProvider
     *
     * @param array $context
     *
     * @return void
     */
    public function testHasherNotInvoked(array $context): void
    {
        $encoderMock = $this->createMock(PasswordHasherInterface::class);
        $persister = new CapsulePersister($encoderMock);

        $encoderMock
            ->expects($this->never())
            ->method('hash')
        ;

        $persister->persist(new Capsule(), $context);
    }

    /**
     * @return array[]
     */
    public function hasherNotInvokedProvider(): array
    {
        return [
            'invalid operation item post' => [
                [
                    'item_operation_name' => 'post',
                ],
            ],
            'invalid operation name put' => [
                [
                    'collection_operation_name' => 'put',
                ],
            ],
            'invalid operation item delete' => [
                [
                    'item_operation_name' => 'delete',
                ],
            ],
        ];
    }

    /**
     * @return void
     */
    public function testSetHashedPassword(): void
    {
        $encoderMock = $this->createMock(PasswordHasherInterface::class);
        $persister = new CapsulePersister($encoderMock);
        $capsuleMock = $this->createMock(Capsule::class);
        $capsuleMock
            ->method('getRawPassword')
            ->willReturn('some-raw-pass')
        ;

        $capsuleMock
            ->expects($this->once())
            ->method('setPassword')
            ->with('hashed-password')
        ;
        $encoderMock
            ->expects($this->once())
            ->method('hash')
            ->willReturn('hashed-password')
        ;

        $persister->persist(
            $capsuleMock,
            [
                'collection_operation_name' => 'post',
            ]
        );
    }
}
