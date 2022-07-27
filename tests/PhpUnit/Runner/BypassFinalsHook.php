<?php

declare(strict_types=1);

namespace App\Tests\PhpUnit\Runner;

use PHPUnit\Runner\BeforeTestHook;
use DG\BypassFinals;

/**
 * Allows to mock/stub final classes.
 */
final class BypassFinalsHook implements BeforeTestHook
{
    /**
     * {@inheritDoc}
     */
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}
