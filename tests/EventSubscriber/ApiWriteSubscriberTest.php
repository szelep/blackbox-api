<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\ApiWriteSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\{
    LimiterInterface,
    RateLimit,
    RateLimiterFactory
};

/**
 * Class ApiWriteSubscriberTest
 */
class ApiWriteSubscriberTest extends TestCase
{
    /**
     * @dataProvider methodsProvider
     *
     * @param string $method
     *
     * @return void
     */
    public function testLimiterNotCalled(string $method): void
    {
        $rateLimiterFactoryMock = $this->createMock(RateLimiterFactory::class);
        $subscriber = new ApiWriteSubscriber($rateLimiterFactoryMock);
        $requestMock = $this->createMock(Request::class);
        $requestMock
            ->method('getMethod')
            ->willReturn($method)
        ;
        $requestEventMock = $this->createMock(RequestEvent::class);
        $requestEventMock
            ->method('getRequest')
            ->willReturn($requestMock)
        ;

        $rateLimiterFactoryMock->expects($this->never())->method('create');

        $subscriber->onDeserialize($requestEventMock);
    }

    /**
     * @return array
     */
    public function methodsProvider(): array
    {
        return [
            'put' => ['put'],
            'get' => ['get'],
            'patch' => ['patch'],
            'delete' => ['delete'],
        ];
    }

    /**
     * @return void
     */
    public function testPostWillConsume(): void
    {
        $rateLimiterFactoryMock = $this->createMock(RateLimiterFactory::class);
        $subscriber = new ApiWriteSubscriber($rateLimiterFactoryMock);
        $requestMock = $this->createMock(Request::class);
        $requestMock
            ->method('getMethod')
            ->willReturn('POST')
        ;
        $requestMock
            ->method('getClientIp')
            ->willReturn('127.0.0.1')
        ;
        $requestEventMock = $this->createMock(RequestEvent::class);
        $requestEventMock
            ->method('getRequest')
            ->willReturn($requestMock)
        ;
        $limiterMock = $this->createMock(LimiterInterface::class);
        $rateLimiterFactoryMock
            ->method('create')
            ->with('127.0.0.1')
            ->willReturn($limiterMock)
        ;
        $rateLimitMock = $this->createMock(RateLimit::class);
        $rateLimitMock
            ->method('isAccepted')
            ->willReturn(true)
        ;

        $limiterMock
            ->expects($this->once())
            ->method('consume')
            ->willReturn($rateLimitMock)
        ;

        $subscriber->onDeserialize($requestEventMock);
    }

    /**
     * @return void
     */
    public function testThrowOnLimitExceeded(): void
    {
        $rateLimiterFactoryMock = $this->createMock(RateLimiterFactory::class);
        $subscriber = new ApiWriteSubscriber($rateLimiterFactoryMock);
        $requestMock = $this->createMock(Request::class);
        $requestMock
            ->method('getMethod')
            ->willReturn('POST')
         ;
        $requestEventMock = $this->createMock(RequestEvent::class);
        $requestEventMock
            ->method('getRequest')
            ->willReturn($requestMock);
        $limiterMock = $this->createMock(LimiterInterface::class);
        $rateLimiterFactoryMock
            ->method('create')
            ->willReturn($limiterMock);
        $limiterMock
            ->method('consume')
            ->willReturn($this->createMock(RateLimit::class))
        ;

        $this->expectException(TooManyRequestsHttpException::class);

        $subscriber->onDeserialize($requestEventMock);
    }
}
