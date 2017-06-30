<?php

namespace Cmhelp\Command\Apache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApacheVhostAddCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('apache:vhost:add')
            ->setDescription('Add virtual host configuration.')
            ->setHelp('This command allows you to add a configuration for a new virtual host...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
