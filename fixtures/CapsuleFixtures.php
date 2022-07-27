<?php

declare(strict_types=1);

namespace App\Fixtures;

use App\Entity\Capsule;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * Class CapsuleFixtures
 */
class CapsuleFixtures extends Fixture
{
    /**
     * Dependency injection.
     *
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(private PasswordHasherInterface $hasher)
    {
    }

    /**
     * @var array
     */
    private const BANK = [
        [
            'id' => 'a5843dab-6d7d-408c-b58e-c70c1df4fc22',
            'publish_at' => '2010-01-01',
            'content' => 'some-content',
            'password' => 'password',
        ],
        [
            'id' => '146e0bf9-240b-4f71-a68f-7296582d89d0',
            'publish_at' => '2060-01-01',
            'content' => 'some-content',
            'password' => 'password',
        ],
        [
            'id' => '231f6aed-caad-4ee3-8e71-d7f334189d62',
            'publish_at' => '2060-01-01',
            'content' => 'some-content',
            'password' => 'password',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $metadata = $manager->getClassMetadata(Capsule::class);
        assert($metadata instanceof ClassMetadata);
        $metadata->setIdGenerator(new AssignedGenerator());

        foreach (self::BANK as $objectData) {
            $capsule = (new Capsule())
                ->setPublishAt(new DateTimeImmutable($objectData['publish_at']))
                ->setContent($objectData['content'])
                ->setPassword($this->hasher->hash($objectData['password']))
            ;

            $metadata->setIdentifierValues($capsule, ['id' => $objectData['id']]);
            $manager->persist($capsule);
        }
        $manager->flush();

        $metadata->setIdGenerator(new UuidGenerator());
    }
}
