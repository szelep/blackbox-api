<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\Http;

use App\Capsule\Domain\Entity\Capsule;
use Symfony\Component\Validator\Constraints as Assert;

class UnpublishCapsuleRequest
{
    #[
        Assert\Expression(
            'value.isPublished()',
            message: 'You can not unpublish capsule that is not published.'
        )
    ]
    public Capsule $capsule;
}
