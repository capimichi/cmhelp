<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/autoload.php';

use Cmhelp\Command\Apache\ApacheVhostAddCommand;
use Cmhelp\Command\Magento\MagentoSetupCommand;
use Cmhelp\Command\UpdateCommand;
use Cmhelp\Command\Xdebug\PhpXdebugCommand;
use Cmhelp\Command\Xdebug\PhpXdebugToggleCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new PhpXdebugCommand());
$application->add(new PhpXdebugToggleCommand());
$application->add(new MagentoSetupCommand());
$application->add(new ApacheVhostAddCommand());
$application->add(new UpdateCommand());

$application->run();
