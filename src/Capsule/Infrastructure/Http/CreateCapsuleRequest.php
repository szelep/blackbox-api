<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\Http;

use App\Capsule\Domain\Entity\Capsule;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCapsuleRequest
{
    #[
        Assert\Sequentially([
            new Assert\Type('string'),
            new Assert\NotBlank(),
            new Assert\Length(max: 10000),
        ]),
        Groups([Capsule::WRITE_GROUP]),
    ]
    public $content;

    #[
        Assert\Sequentially([
            new Assert\Type('string'),
            new Assert\NotBlank(),
            new Assert\Length(max: 200),
        ]),
        Groups([Capsule::WRITE_GROUP]),
    ]
    public $password;

    #[
        Assert\Sequentially([
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\DateTime()
        ]),
        Groups([Capsule::WRITE_GROUP]),
    ]
    public $publishAt;

    public function setContent($content): void
    {
        \Webmozart\Assert\Assert::string($content);

        $allowedHtmlTags = [
            'p',
            'ul',
            'li',
            'strong',
            'i',
            'figure',
            'tbody',
            'tr',
            'td',
            'blockquote',
            'table',
            'h2',
            'h3',
            'h4',
        ];
        $this->content = strip_tags($content, $allowedHtmlTags);
    }
}
