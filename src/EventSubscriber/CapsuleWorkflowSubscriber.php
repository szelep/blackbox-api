<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Capsule;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;

/**
 * Subscriber for Capsule transition events.
 */
class CapsuleWorkflowSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.capsule.completed.unpublish' => 'onUnpublish',
        ];
    }

    /**
     * Handles operation on unpublished capsule.
     *
     * It will set publishAt date to year 2500.
     *
     * @param CompletedEvent $event
     *
     * @return void
     */
    public function onUnpublish(CompletedEvent $event): void
    {
        $capsule = $event->getSubject();
        assert($capsule instanceof Capsule);
        $capsule->setPublishAt(new DateTimeImmutable('2500-01-01'));
    }
}
