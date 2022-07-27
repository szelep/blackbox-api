<?php

use App\Tests\PhpUnit\CommandExecutor;
use DG\BypassFinals;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
BypassFinals::enable();
$dbConfig = new CommandExecutor();
$dbConfig->dropDatabase();
$dbConfig->createDatabase();
$dbConfig->createSchema();
$dbConfig->loadFixtures();
$dbConfig->runPublisher();
