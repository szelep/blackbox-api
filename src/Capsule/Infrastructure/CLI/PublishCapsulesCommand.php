<?php

declare(strict_types=1);

namespace App\Capsule\Infrastructure\CLI;

use App\Capsule\Domain\Entity\Capsule;
use App\Capsule\Domain\Repository\CapsuleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Loop over capsules that publication date is after "now" and publishes them.
 */
#[AsCommand(
    name: 'app:publish-capsules',
    description: 'Loop over capsules that publication date is after "now" and publishes them.',
)]
class PublishCapsulesCommand extends Command
{
    /**
     * Dependency injection.
     *
     * @param WorkflowInterface $capsuleStateMachine
     * @param EntityManagerInterface $entityManager
     * @param CapsuleRepositoryInterface $repository
     * @param string|null $name
     */
    public function __construct(
        private WorkflowInterface $capsuleStateMachine,
        private EntityManagerInterface $entityManager,
        private CapsuleRepositoryInterface $repository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entityManager = $this->entityManager;
        /** @var Capsule[] $awaitsForPublication */
        $awaitsForPublication = $this->repository->findAllForPublication();

        $counter = 0;
        foreach ($awaitsForPublication as $capsule) {
            if (!$this->capsuleStateMachine->can($capsule, 'publish')) {
                $io->warning(sprintf(
                    'Capsule "%s" can not be published.',
                    $capsule->getId()
                ));

                continue;
            }
            $this->capsuleStateMachine->apply($capsule, 'publish');
            $counter++;
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            '%s capsules were published!',
            $counter
        ));

        return Command::SUCCESS;
    }
}
