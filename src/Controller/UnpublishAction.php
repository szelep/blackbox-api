<?php

declare(strict_types=1);

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Capsule;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Allows capsule owner to unpublish it.
 */
#[AsController]
class UnpublishAction
{
    /**
     * Dependency injection.
     *
     * @param ValidatorInterface $validator
     * @param WorkflowInterface $capsuleStateMachine
     */
    public function __construct(private ValidatorInterface $validator, private WorkflowInterface $capsuleStateMachine)
    {
    }

    /**
     * Handle capsule unpublishing.
     *
     * @param Capsule $data
     *
     * @return Capsule
     */
    public function __invoke(Capsule $data): Capsule
    {
        $this->validator->validate(
            $data,
            ['groups' => [Capsule::UNPUBLISH_GROUP]]
        );

        if (!$this->capsuleStateMachine->can($data, 'unpublish')) {
            throw new TransitionException(
                $data,
                'unpublish',
                $this->capsuleStateMachine,
                'Unable to unpublish capsule.'
            );
        }

        $this->capsuleStateMachine->apply($data, 'unpublish');

        return $data;
    }
}
