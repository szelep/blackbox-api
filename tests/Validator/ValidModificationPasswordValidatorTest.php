<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Domain\Entity\Capsule;
use App\Validator\{ValidModificationPassword, ValidModificationPasswordValidator};
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class ValidModificationPasswordValidator
 */
class ValidModificationPasswordValidatorTest extends ConstraintValidatorTestCase
{
    private PasswordHasherInterface|MockObject|null $hasherMock = null;

    /**
     * @var ValidModificationPasswordValidator
     */
    protected $validator;

    /**
     * {@inheritDoc}
     */
    protected function createValidator(): ConstraintValidator
    {
        $this->hasherMock = $this->createMock(PasswordHasherInterface::class);
        return new ValidModificationPasswordValidator($this->hasherMock);
    }

    /**
     * @return void
     */
    public function testNoViolationOnUnsupported(): void
    {
        $this->setObject(new stdClass());

        $this->validator->validate('test-password', $this->constraint);

        $this->assertNoViolation();
    }

    /**
     * @return void
     */
    public function testNoViolationOnValidHasherVerification(): void
    {
        $this
            ->hasherMock
            ->method('verify')
            ->willReturn(true)
        ;
        $this->setObject((new Capsule())->setPassword('pass'));

        $this->validator->validate('test-password', $this->constraint);

        $this->assertNoViolation();
    }

    /**
     * @return void
     */
    public function testViolationOnInvalidVerification(): void
    {
        $this
            ->hasherMock
            ->method('verify')
            ->willReturn(false)
        ;
        $this->setObject((new Capsule())->setPassword('pass'));
        $constraint = new ValidModificationPassword();

        $this->validator->validate('test-password', $constraint);

        $this->buildViolation($constraint->message)
            ->setCode($constraint::INVALID_ERROR)
            ->assertRaised()
        ;
    }
}
