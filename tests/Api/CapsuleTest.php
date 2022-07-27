<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Double\RequestHelper;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CapsuleTest
 */
class CapsuleTest extends ApiTestCase
{
    use RequestHelper;

    /**
     * @return void
     */
    public function testUnableToFetchCollection(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessageMatches('/Method Not Allowed (Allow: POST)*/');

        $this->request('/api/capsules');
    }

    /**
     * @testdox response should be successfull and content be visible
     *
     * @return void
     */
    public function testGetItem(): void
    {
        $this->request('/api/capsules/a5843dab-6d7d-408c-b58e-c70c1df4fc22');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Capsule',
            '@id' => '/api/capsules/a5843dab-6d7d-408c-b58e-c70c1df4fc22',
            '@type' => 'Capsule',
            'content' => 'some-content',
            'publishAt' => '2010-01-01T00:00:00+00:00',
            'status' => 'published',
            'id' => 'a5843dab-6d7d-408c-b58e-c70c1df4fc22',
            'published' => true,
        ]);
    }

    /**
     * @return void
     */
    public function testGetNotPublished(): void
    {
        $this->request('/api/capsules/146e0bf9-240b-4f71-a68f-7296582d89d0');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Capsule',
            '@id' => '/api/capsules/146e0bf9-240b-4f71-a68f-7296582d89d0',
            '@type' => 'Capsule',
            'content' => null,
            'publishAt' => '2060-01-01T00:00:00+00:00',
            'status' => 'queued',
            'id' => '146e0bf9-240b-4f71-a68f-7296582d89d0',
            'published' => false,
        ]);
    }

    /**
     * @return void
     */
    public function testUnpublishItemInvalidPassword(): void
    {
        $this->request(
            '/api/capsules/a5843dab-6d7d-408c-b58e-c70c1df4fc22/unpublish',
            method: Request::METHOD_PUT,
            payload: [
                'modificationPassword' => 'invalid-password'
            ],
            throwException: false
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'modificationPassword: Invalid modification password']);
    }

    /**
     * @return void
     */
    public function testSuccessfullUnpublish(): void
    {
        $this->request(
            '/api/capsules/a5843dab-6d7d-408c-b58e-c70c1df4fc22/unpublish',
            method: Request::METHOD_PUT,
            payload: [
                'modificationPassword' => 'password'
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Capsule',
            '@id' => '/api/capsules/a5843dab-6d7d-408c-b58e-c70c1df4fc22',
            '@type' => 'Capsule',
            'content' => null,
            'rawPassword' => null,
            'modificationPassword' => 'password',
            'publishAt' => '2500-01-01T00:00:00+00:00',
            'status' => 'queued',
            'id' => 'a5843dab-6d7d-408c-b58e-c70c1df4fc22',
            'published' => false,
        ]);
    }

    /**
     * @return void
     */
    public function testModificationNotAllowedForPublished(): void
    {
        $this->request(
            '/api/capsules/a5843dab-6d7d-408c-b58e-c70c1df4fc22',
            method: Request::METHOD_PUT,
            payload: [
                'modificationPassword' => 'password',
                'publishAt' => '2050-01-01',
            ],
            throwException: false
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'Modification is not available for published capsules.']);
    }

    /**
     * @return void
     */
    public function testModifyInvalidPassword(): void
    {
        $this->request(
            '/api/capsules/146e0bf9-240b-4f71-a68f-7296582d89d0',
            method: Request::METHOD_PUT,
            payload: [
                'modificationPassword' => 'invalid-password',
                'publishAt' => '2050-01-01',
            ],
            throwException: false
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['hydra:description' => 'modificationPassword: Invalid modification password']);
    }

    /**
     * @return void
     */
    public function testSuccessfullModify(): void
    {
        $this->request(
            '/api/capsules/146e0bf9-240b-4f71-a68f-7296582d89d0',
            method: Request::METHOD_PUT,
            payload: [
                'modificationPassword' => 'password',
                'publishAt' => '2050-01-01',
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Capsule',
            '@id' => '/api/capsules/146e0bf9-240b-4f71-a68f-7296582d89d0',
            '@type' => 'Capsule',
            'content' => null,
            'publishAt' => '2050-01-01T00:00:00+00:00',
            'status' => 'queued',
            'id' => '146e0bf9-240b-4f71-a68f-7296582d89d0',
            'published' => false,
        ]);
    }

    /**
     * @return void
     */
    public function testSuccessfullCreate(): void
    {
        $this->request(
            '/api/capsules',
            method: Request::METHOD_POST,
            payload: [
                'rawPassword' => 'some-password',
                'publishAt' => '2050-01-01',
                'content' => 'someee content',
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/Capsule',
            '@type' => 'Capsule',
            'content' => null,
            'publishAt' => '2050-01-01T00:00:00+00:00',
            'status' => 'queued',
            'published' => false,
        ]);
    }

    /**
     * @return void
     */
    public function testCreateConstraintsViolation(): void
    {
        $this->request(
            '/api/capsules',
            method: Request::METHOD_POST,
            payload: [],
            throwException: false
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'hydra:description' => 'content: This value should not be blank.
rawPassword: This value should not be blank.
publishAt: This value should not be blank.'
        ]);
    }
}
