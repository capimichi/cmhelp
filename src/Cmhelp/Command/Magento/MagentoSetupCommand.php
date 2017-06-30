<?php

namespace Cmhelp\Command\Magento;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoSetupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('magento:setup')
            ->setDescription('Handle magento setup.')
            ->setHelp('This command allows you to install magento on local environment...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
