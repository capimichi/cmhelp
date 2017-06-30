<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Cmhelp\Command\PhpXdebugActivateCommand;
use Cmhelp\Command\PhpXdebugCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new PhpXdebugCommand());
$application->add(new PhpXdebugActivateCommand());

$application->run();
