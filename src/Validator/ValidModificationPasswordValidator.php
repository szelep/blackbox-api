<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\Capsule;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\{
    Constraint,
    ConstraintValidator
};

/**
 * ValidModificationPassword constraint validator.
 *
 * It only supports {@link Capsule} as context object.
 * For more details see {@link ValidModificationPassword}.
 */
class ValidModificationPasswordValidator extends ConstraintValidator
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
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        assert($constraint instanceof ValidModificationPassword);
        $contextObject = $this->context->getObject();
        if (!$contextObject instanceof Capsule) {
            return;
        }

        $verificationResult = $this
            ->hasher
            ->verify(
                $contextObject->getPassword(),
                $value ?? ''
            );

        if (!$verificationResult) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setCode($constraint::INVALID_ERROR)
                ->addViolation()
            ;
        }
    }
}
