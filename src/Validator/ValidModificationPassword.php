<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint that compares provided modification password provided by user and hashed from Capsule.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidModificationPassword extends Constraint
{
    /**
     * Error codes.
     *
     * @var string
     */
    public const INVALID_ERROR = 'b6f862eb-0118-4cca-a85e-d6d803dd5b8f';

    public string $message = 'Invalid modification password';
}
