<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Domain\Entity\Capsule;
use App\EventSubscriber\CapsuleWorkflowSubscriber;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

/**
 * Class CapsuleWorkflowSubscriberTest
 */
class CapsuleWorkflowSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetsFutureDate(): void
    {
        $subscriber = new CapsuleWorkflowSubscriber();
        $capsuleMock = $this->createMock(Capsule::class);

        $capsuleMock
            ->expects($this->once())
            ->method('setPublishAt')
            ->with(new DateTimeImmutable('2500-01-01'))
        ;


        $subscriber->onUnpublish(new CompletedEvent(
            $capsuleMock,
            $this->createMock(Marking::class)
        ));
    }
}
