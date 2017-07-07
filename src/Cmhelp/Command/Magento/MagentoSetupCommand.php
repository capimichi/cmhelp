<?php

namespace Cmhelp\Command\Magento;

use Cmhelp\Utils\DbManager;
use Cmhelp\Utils\LinuxVirtualHostManager;
use Cmhelp\Utils\MageDbManager;
use Cmhelp\Utils\MageUserManager;
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

        $question = new Question("Hostname (website.local) [] ", null);
        $hostName = $questionHelper->ask($input, $output, $question);

        $hostNameWithoutExtension = preg_replace("/\.[^.]*$/is", "", $hostName);

        $question = new Question("Host [localhost] ", 'localhost');
        $host = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db User [root] ", 'root');
        $dbUser = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db Pwd [] ", '');
        $dbPwd = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db Name [magento_{$hostNameWithoutExtension}] ", "magento_{$hostNameWithoutExtension}");
        $dbName = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db port [3306] ", '3306');
        $dbPort = $questionHelper->ask($input, $output, $question);

        $question = new Question("Db file path [] ", null);
        $dbFilePath = $questionHelper->ask($input, $output, $question);
        $dbFilePath = $dbFilePath ? str_replace("'", "", $dbFilePath) : null;

        $websitePath = null;
        $question = new Question("Website path (/var/www/website.com/) [] ", null);
        while ($websitePath == null) {
            $websitePath = $questionHelper->ask($input, $output, $question);
            if (!file_exists(rtrim($websitePath, "/") . "/app/Mage.php")) {
                $websitePath = null;
                $output->writeln("ERROR: Not a Magento directory root");
            }
        }

        $question = new Question("Protocol (http|https) [http] ", 'http');
        $hostProtocol = $questionHelper->ask($input, $output, $question);

        $question = new Question("Session save (db|files) [db] ", 'db');
        $sessionSave = $questionHelper->ask($input, $output, $question);

        $question = new Question("OS (linux|osx) [linux] ", 'linux');
        $os = $questionHelper->ask($input, $output, $question);

        $output->writeln("");
        $output->writeln("Creating vhost configuration ...");

        if ($os == "osx") {
            $question = new Question("Vhost file path (/Applications/XAMP/vhost.conf) [] ", null);
            $vhostFilePath = $questionHelper->ask($input, $output, $question);

        } else {
            $linuxVirtualHostManager = new LinuxVirtualHostManager();
            $linuxVirtualHostManager->addVirtualHost($hostName, $websitePath);
            $linuxVirtualHostManager->addHostname($host, $hostName);
        }

        $output->writeln("Changing local.xml ...");

        $localXmlPath = rtrim($websitePath, "/") . "/app/etc/local.xml";
        if (!is_readable($localXmlPath) || !is_writable($localXmlPath)) {
            die("local.xml not writable");
        }

        $localXmlContent = file_get_contents($localXmlPath);
        $localXmlContent = preg_replace('/<host>.*?<\/host>/is', "<host><![CDATA[{$host}]]></host>", $localXmlContent);
        $localXmlContent = preg_replace('/<username>.*?<\/username>/is', "<username><![CDATA[{$dbUser}]]></username>", $localXmlContent);
        $localXmlContent = preg_replace('/<password>.*?<\/password>/is', "<password><![CDATA[{$dbPwd}]]></password>", $localXmlContent);
        $localXmlContent = preg_replace('/<dbname>.*?<\/dbname>/is', "<dbname><![CDATA[{$dbName}]]></dbname>", $localXmlContent);
        $localXmlContent = preg_replace('/<session_save>.*?<\/session_save>/is', "<session_save><![CDATA[{$sessionSave}]]></session_save>", $localXmlContent);
        file_put_contents($localXmlPath, $localXmlContent);

        if ($dbFilePath) {

            $output->writeln("Importing database ...");
            $dbManager = new DbManager($host, $dbUser, $dbPwd);
            $dbManager->createDatabase($dbName, true);
            $dbManager->importDatabaseFromFile($dbName, $dbFilePath);
        }

        $output->writeln("Changing database hostname ...");

        $dbBaseUrl = $hostProtocol . "://" . rtrim($hostName, "/") . "/";

        $mageDbManager = new MageDbManager($host, $dbUser, $dbPwd);
        $mageDbManager->changeBaseUrl($dbName, $dbBaseUrl);

        $output->writeln("Creating user user: 'local' pwd: 'local'.");
        $mageUserManager = new MageUserManager($websitePath);
        if (!in_array("local", $mageUserManager->getUsernames())) {
            $mageUserManager->addAdmin("local", "local", "local", "tech@internetsm.com", "local");
        }

        $output->writeln("Configuration completed, please restart apache.");
    }
}
