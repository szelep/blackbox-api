<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\Symfony\Validator;

use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Infrastructure\Http\ChangePublishDateRequest;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\{
    Constraint,
    ConstraintValidator
};

/**
 * ValidModificationPassword constraint validator.
 *
 * It only supports {@link ChangePublishDateRequest} as context object.
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
        if (!$contextObject instanceof ChangePublishDateRequest) {
            return;
        }

        $verificationResult = $this
            ->hasher
            ->verify(
                (string) $contextObject->capsule->getPassword(),
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
