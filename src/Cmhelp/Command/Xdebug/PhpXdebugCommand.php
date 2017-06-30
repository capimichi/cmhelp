<?php

namespace Cmhelp\Command\Xdebug;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
        $questionHelper = $this->getHelper('question');

        $question = new Question("Database host [localhost]", 'localhost');
        $host = $questionHelper->ask($input, $output, $question);

        $question = new Question("Database user [root]", 'root');
        $root = $questionHelper->ask($input, $output, $question);

        $question = new Question("Database pwd []", '');
        $password = $questionHelper->ask($input, $output, $question);

        $question = new Question("Hostname (http://website.local) []", null);
        $hostname = $questionHelper->ask($input, $output, $question);

        $question = new Question("Table name (magento_website) []", null);
        $tableName = $questionHelper->ask($input, $output, $question);



    }
}
