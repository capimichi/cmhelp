<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/autoload.php';

use Cmhelp\Command\Xdebug\PhpXdebugActivateCommand;
use Cmhelp\Command\Xdebug\PhpXdebugCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new PhpXdebugCommand());
$application->add(new PhpXdebugActivateCommand());

$application->run();
