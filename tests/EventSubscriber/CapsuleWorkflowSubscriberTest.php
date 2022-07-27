<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Entity\Capsule;
use App\EventSubscriber\CapsuleWorkflowSubscriber;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Event\CompletedEvent;

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
        $eventMock = $this->createMock(CompletedEvent::class);
        $eventMock->method('getSubject')->willReturn($capsuleMock);

        $capsuleMock
            ->expects($this->once())
            ->method('setPublishAt')
            ->with(new DateTimeImmutable('2500-01-01'));


        $subscriber->onUnpublish($eventMock);
    }
}
