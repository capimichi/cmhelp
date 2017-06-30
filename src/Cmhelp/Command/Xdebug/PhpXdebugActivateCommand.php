<?php

namespace Cmhelp\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpXdebugActivateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('php:xdebug:activate')
            ->setDescription('Activate xdebug.')
            ->setHelp('This command allows you to enable xdebug...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
