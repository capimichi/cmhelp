<?php

namespace Cmhelp\Command\Xdebug;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpXdebugCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('php:xdebug')
            ->setDescription('Handle xdebug settings.')
            ->setHelp('This command allows you to edit xdebug settings...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
