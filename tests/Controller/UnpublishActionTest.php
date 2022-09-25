<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\UnpublishAction;
use App\Domain\Entity\Capsule;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class UnpublishActionTest
 */
class UnpublishActionTest extends TestCase
{
    /**
     * @return void
     */
    public function testValidateWithExpectedGroups(): void
    {
        $capsuleMock = $this->createMock(Capsule::class);
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $workflowMock = $this->createMock(WorkflowInterface::class);
        $workflowMock
            ->method('can')
            ->willReturn(true)
        ;
        $controller = new UnpublishAction(
            $validatorMock,
            $workflowMock
        );

        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with(
                $capsuleMock,
                [
                    'groups' => [Capsule::UNPUBLISH_GROUP],
                ]
            )
        ;

        $controller($capsuleMock);
    }

    /**
     * @return void
     */
    public function testThrowOnWorkflowCanNot(): void
    {
        $capsuleMock = $this->createMock(Capsule::class);
        $workflowMock = $this->createMock(WorkflowInterface::class);
        $workflowMock
            ->method('can')
            ->willReturn(false)
        ;
        $controller = new UnpublishAction(
            $this->createMock(ValidatorInterface::class),
            $workflowMock
        );

        $this->expectException(TransitionException::class);
        $this->expectExceptionMessage('Unable to unpublish capsule.');

        $controller($capsuleMock);
    }

    /**
     * @return void
     */
    public function testUnpublishResponse():  void
    {
        $capsuleMock = $this->createMock(Capsule::class);
        $workflowMock = $this->createMock(WorkflowInterface::class);
        $workflowMock
            ->method('can')
            ->willReturn(true)
        ;
        $controller = new UnpublishAction(
            $this->createMock(ValidatorInterface::class),
            $workflowMock
        );

        $workflowMock
            ->expects($this->once())
            ->method('apply')
            ->with(
                $capsuleMock,
                'unpublish'
            );

        $response = $controller($capsuleMock);

        $this->assertInstanceOf(Capsule::class, $response);
    }
}
