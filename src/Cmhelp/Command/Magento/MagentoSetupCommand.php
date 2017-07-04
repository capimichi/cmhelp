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
            $vhostConfigPath = "/etc/apache2/sites-available/{$hostName}";
            $vhostProdConfigPath = "/etc/apache2/sites-enabled/{$hostName}";
            if (!is_writable(dirname($vhostConfigPath)) || !is_writable(dirname($vhostProdConfigPath))) {
                die("Cannot write virtual host configuration");
            }
            $vhostContent = "
                <VirtualHost *:80>
                    ServerName {$hostName}
                    ServerAdmin webmaster@localhost
                    DocumentRoot {$hostPath}
                    ErrorLog $\{APACHE_LOG_DIR}/{$hostName}_error.log
                    CustomLog $\{APACHE_LOG_DIR}/{$hostName}_access.log combined
                </VirtualHost>
                # vim: syntax=apache ts=4 sw=4 sts=4 sr noet
            ";
            file_put_contents($vhostConfigPath, $vhostContent);

            if (!file_exists($vhostProdConfigPath)) {
                symlink($vhostConfigPath, $vhostProdConfigPath);
            }

            $hostsPath = "/etc/hosts";
            if (!is_readable($hostsPath) || !is_writable($hostsPath)) {
                die("Cannot read/write {$hostsPath}");
            }
            $hostsContent = file_get_contents($hostsPath);

            if (!preg_match("/{$host}\t{$hostName}/is", $hostsContent)) {
                $hostsContent .= "\n{$host}\t{{$hostName}";
            }

            file_put_contents($hostsPath, $hostsContent);
        }

        $localXmlPath = rtrim($hostPath, "/") . "/app/etc/local.xml";
        if (!is_readable($localXmlPath) || !is_writable($localXmlPath)) {
            die("local.xml not writable");
        }

        // Change local XML
        $localXmlContent = file_get_contents($localXmlPath);
        $localXmlContent = preg_replace('/<host>.*?<\/host>/is', "<host><![CDATA[{$host}]]></host>", $localXmlContent);
        $localXmlContent = preg_replace('/<username>.*?<\/username>/is', "<username><![CDATA[{$dbUser}]]></username>", $localXmlContent);
        $localXmlContent = preg_replace('/<password><![CDATA[capim]]><\/password>/is', "<password><![CDATA[{$dbPwd}]]></password>", $localXmlContent);
        $localXmlContent = preg_replace('/<dbname>.*?<\/dbname>/is', "<dbname><![CDATA[{$dbName}]]></dbname>", $localXmlContent);
        $localXmlContent = preg_replace('/<session_save>.*?<\/session_save>/is', "<session_save><![CDATA[{$sessionSave}]]></session_save>", $localXmlContent);
        file_put_contents($localXmlPath, $localXmlContent);

        if ($dbFilePath) {
            // Import DB
            $conn = new \mysqli($host, $dbUser, $dbPwd);
            if ($conn->connect_error) {
                die("Impossibile connetersi al database: " . $conn->connect_error);
            }
            $dropQuery = "DROP DATABASE {$dbName}";
            $createQuery = "CREATE DATABASE {$dbName}";
            $conn->query($dropQuery);
            $conn->query($createQuery);
            $conn->close();

            // Change DB host name
            $conn = new \mysqli($host, $dbUser, $dbPwd, $dbName);
            if ($conn->connect_error) {
                die("Impossibile connetersi al database: " . $conn->connect_error);
            }
            $queryUnsecureUrl = "update core_config_data set value = '{$hostProtocol}://{$hostName}' where path = 'web/unsecure/base_url'; ";
            $querySecureUrl = "update core_config_data set value = '{$hostProtocol}://{$hostName}' where path = 'web/secure/base_url';";
            $conn->query($queryUnsecureUrl);
            $conn->query($querySecureUrl);
            $conn->close();
        }
    }
}
