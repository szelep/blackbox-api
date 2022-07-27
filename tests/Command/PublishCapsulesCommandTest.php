<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class PublishCapsulesCommandTest
 */
class PublishCapsulesCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('app:publish-capsules');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @return void
     */
    public function testExecuteOutput(): void
    {
        $this
            ->commandTester
            ->execute([])
        ;
        $output = $this
            ->commandTester
            ->getDisplay()
        ;

        $this->assertStringContainsString(' [OK] 0 capsules were published!', $output);
    }
}
