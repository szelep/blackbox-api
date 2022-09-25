<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\ApiPlatform\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * Subscribe pre deserialize events for request POST method.
 */
class ApiWriteSubscriber implements EventSubscriberInterface
{
    /**
     * Dependency injection.
     *
     * @param RateLimiterFactory $apiPostLimiter
     */
    public function __construct(private RateLimiterFactory $apiPostLimiter)
    {
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onDeserialize', EventPriorities::PRE_DESERIALIZE],
        ];
    }

    /**
     * Prevents clients from creating more objects than is set in rate_limiter setting.
     *
     * @param RequestEvent $event
     *
     * @throws TooManyRequestsHttpException on rate limit exceeded
     *
     * @return void
     */
    public function onDeserialize(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getMethod() !== Request::METHOD_POST) {
            return;
        }

        $limiter = $this
            ->apiPostLimiter
            ->create($request->getClientIp())
        ;
        $limit = $limiter->consume();
        if (!$limit->isAccepted()) {
            throw new TooManyRequestsHttpException(
                $limit->getRetryAfter()->getTimestamp(),
                headers: [
                    'X-RateLimit-Limit' => $limit->getLimit(),
                    'X-RateLimit-Remaining' => $limit->getRemainingTokens(),
                ]
            );
        }
    }
}
