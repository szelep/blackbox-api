<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Capsule;
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
     * @param string|null $name
     */
    public function __construct(
        private WorkflowInterface $capsuleStateMachine,
        private EntityManagerInterface $entityManager,
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
        $awaitsForPublication = $entityManager
            ->getRepository(Capsule::class)
            ->findAllForPublication()
        ;

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
