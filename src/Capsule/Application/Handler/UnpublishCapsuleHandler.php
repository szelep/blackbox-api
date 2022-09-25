<?php

declare(strict_types=1);

namespace App\Capsule\Application\Handler;

use App\Capsule\Application\Command\CreateCapsule;
use App\Capsule\Application\Command\UnpublishCapsule;
use App\Capsule\Domain\Entity\{
    Capsule,
    CapsuleContent,
    CapsuleHashedPassword,
    CapsuleId,
    PublishAt
};
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

class UnpublishCapsuleHandler
{
    /**
     * Public constructor.
     *
     * @param WorkflowInterface $capsuleStateMachine
     * @param CapsuleRepositoryInterface $repository
     */
    public function __construct(
        private WorkflowInterface $capsuleStateMachine,
        private CapsuleRepositoryInterface $repository
    ) {
    }

    public function __invoke(UnpublishCapsule $command): void
    {
        $capsule = $this->repository->getById($command->getId());

        if (!$this->capsuleStateMachine->can($capsule, 'unpublish')) {
            throw new TransitionException(
                $capsule,
                'unpublish',
                $this->capsuleStateMachine,
                'Unable to unpublish capsule.'
            );
        }

        $this->capsuleStateMachine->apply($capsule, 'unpublish');
    }
}
