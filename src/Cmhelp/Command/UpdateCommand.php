<?php

namespace Cmhelp\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class UpdateCommand extends Command
{

    const UPDATE_URL = "https://github.com/capimichi/cmhelp/blob/master/build/cmhelp.phar?raw=true";

    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Update executable.')
            ->setHelp('This command allows you to update cmhelp...');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentFile = \Phar::running(false);
        $output->writeln("Connecting...");

        if (!is_readable($currentFile)) {
            die("Cannot read {$currentFile}");
        }

        if (!is_writable($currentFile)) {
            die("Cannot write {$currentFile}");
        }

        $localHash = md5(file_get_contents($currentFile));

        $remoteFileContent = file_get_contents(self::UPDATE_URL);
        $remoteHash = md5($remoteFileContent);

        if ($localHash == $remoteHash) {
            $output->writeln("Already at latest version.");
        } else {
            $output->writeln("Installing...");
            file_put_contents($currentFile, $remoteFileContent);
            $output->writeln("Updated.");
        }

    }
}
