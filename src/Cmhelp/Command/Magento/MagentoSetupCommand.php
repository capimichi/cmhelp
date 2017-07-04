<?php

namespace Cmhelp\Command\Magento;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MagentoSetupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('magento:setup')
            ->setDescription('Handle magento setup.')
            ->addOption('mac', 'm', InputOption::VALUE_NONE, 'If is on OSX platform', null)
            ->addUsage('sudo cmhelp magento:setup')
            ->setHelp('This command allows you to install magento on local environment...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');

        $question = new Question("Host [localhost]", 'localhost');
        $host = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db User [root]", 'root');
        $dbUser = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db Pwd []", '');
        $dbPwd = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db Name []", null);
        $dbName = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db port [3306]", '3306');
        $dbPort = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db file path []", null);
        $dbFilePath = $questionHelper->ask($input, $output, $question);

        $question = new Question("Hostname (website.local) []", null);
        $hostName = $questionHelper->ask($input, $output, $question);

        $question = new Question("Website path (/var/www/website.com/) []", null);
        $hostPath = $questionHelper->ask($input, $output, $question);

        $question = new Question("Protocol (http|https) [http]", 'http');
        $hostProtocol = $questionHelper->ask($input, $output, $question);

        $question = new Question("Session save (db|files) [db]", 'db');
        $sessionSave = $questionHelper->ask($input, $output, $question);

        $question = new Question("OS (linux|osx) [linux]", 'linux');
        $os = $questionHelper->ask($input, $output, $question);

        if ($os == "osx") {
            $question = new Question("Vhost file path (/Applications/XAMP/vhost.conf) []", null);
            $vhostFilePath = $questionHelper->ask($input, $output, $question);

        } else {


        }

    }
}
