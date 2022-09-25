<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\Symfony\Workflow\EventSubscriber;

use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;

/**
 * Subscriber for Capsule transition events.
 */
class CapsuleWorkflowSubscriber implements EventSubscriberInterface
{
    public function __construct(private CapsuleRepositoryInterface $repository)
    {
    }

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
     * @param CompletedEvent $event
     *
     * @return void
     */
    public function onUnpublish(CompletedEvent $event): void
    {
        $capsule = $event->getSubject();
        assert($capsule instanceof Capsule);

        $capsule->unpublish();

        $this->repository->save($capsule);
    }
}
