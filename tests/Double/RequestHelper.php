<?php

declare(strict_types=1);

namespace App\Tests\Double;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait RequestHelper.
 *
 * Provides client creator and short request method alias.
 *
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
trait RequestHelper
{
    protected Client|null $client = null;



    /**
     * Gets base client.
     */
    protected function getBaseClient(): Client
    {
        if (!method_exists(self::class, 'createClient')) {
            throw new RuntimeException('Trait can be used only within ApiTestCase');
        }

        return static::createClient();
    }

    /**
     * Make request with defined options.
     *
     * @param string $apiPath
     * @param string $method
     * @param array $payload
     * @param array $headers
     * @param bool $throwException throw exception on 3/4/5xx response code
     *
     * @throws RuntimeException on response json_decode failure
     *
     * @return array
     */
    protected function request(
        string $apiPath,
        string $method = Request::METHOD_GET,
        array $payload = [],
        array $headers = [],
        bool $throwException = true
    ): array {
        $client = $this->client ?? $this->getBaseClient();
        $response = $client->request(
            $method,
            $apiPath,
            [
                'headers' => $headers,
                'json' => $payload,
                'extra' => [
                    'files' => $headers['files'] ?? [],
                ]
            ]
        );

        $responseContent = $response->getContent($throwException);
        $jsonDecoded = json_decode($responseContent, true) ?? [];
        if (json_last_error() !== JSON_ERROR_NONE && $method !== Request::METHOD_DELETE) {
            var_dump($responseContent);
            $this->fail('Unable to parse JSON response.');
        }

        return $jsonDecoded;
    }
}
