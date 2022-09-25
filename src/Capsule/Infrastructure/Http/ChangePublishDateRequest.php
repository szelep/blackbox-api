<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\Http;

use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Infrastructure\Symfony\Validator\ValidModificationPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePublishDateRequest
{
    #[
        Assert\Expression(
            'value.getStatus() !== published_status',
            message: 'Modification is not available for published capsules.',
            values: [
                'published_status' => Capsule::STATUS_PUBLISHED
            ],
        ),
        Groups([Capsule::WRITE_GROUP]),
    ]
    public Capsule $capsule;

    #[
        Assert\Sequentially([
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new ValidModificationPassword()
        ]),
        Groups([Capsule::WRITE_GROUP])
    ]
    public $password;

    #[
        Assert\Sequentially([
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\DateTime(),
        ]),
        Groups([Capsule::WRITE_GROUP]),
    ]
    public $publishAt;
}
