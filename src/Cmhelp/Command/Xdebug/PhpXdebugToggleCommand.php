<?php

namespace Cmhelp\Command\Xdebug;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class PhpXdebugToggleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('php:xdebug:toggle')
            ->setDescription('Toggle xdebug.')
            ->setHelp('This command allows you to toggle xdebug on/off...')
            ->addArgument('status', InputArgument::REQUIRED, "on/off")
            ->addOption('idekey', 'k', InputOption::VALUE_REQUIRED, "The ide key used to locate xdebug", null)
            ->addOption('port', 'p', InputOption::VALUE_REQUIRED, "The port used to locate xdebug", null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');

        $toggle = $input->getArgument('status');
        $toggle = ($toggle == "on") ? true : false;

        $idekey = $input->getOption('idekey');
        if (!$idekey) {
            if ($toggle) {
                $question = new Question("Insert your idekey ", null);
                $idekey = $questionHelper->ask($input, $output, $question);
            }
        }

        $port = $input->getOption('port');
        if (!$port) {
            if ($toggle) {
                $question = new Question("Insert port ", null);
                $port = $questionHelper->ask($input, $output, $question);
            }
        }

        $inipath = php_ini_loaded_file();
        $question = new ConfirmationQuestion("Is this your current php.ini file? {$inipath} [Y/n]", true);
        if (!$questionHelper->ask($input, $output, $question)) {
            $question = new Question("Insert your current php.ini file path ", null);
            $inipath = $questionHelper->ask($input, $output, $question);
        }
        if (!is_readable($inipath)) {
            die("Cannot read php.ini file");
        }

        $iniContent = file_get_contents($inipath);

        $xdebugLines = [
            "zend_extension=xdebug.so",
            "xdebug.force_display_errors=1",
            "xdebug.force_error_reporting=1",
            "xdebug.cli_color=1",
            "xdebug.var_display_max_children=-1",
            "xdebug.var_display_max_data=1024",
            "xdebug.var_display_max_depth=50",
            "xdebug.max_nesting_level=1024",
            "xdebug.remote_enable=1",
            "xdebug.idekey={$idekey}",
            "xdebug.remote_autostart=1",
            "xdebug.remote_port={$port}",
        ];

        $xdebugRegexLines = [
            "zend_extension=xdebug\.so",
            "xdebug\.force_display_errors=.*",
            "xdebug\.force_error_reporting=.*",
            "xdebug\.cli_color=.*",
            "xdebug\.var_display_max_children=-.*",
            "xdebug\.var_display_max_data=.*",
            "xdebug\.var_display_max_depth=.*",
            "xdebug\.max_nesting_level=.*",
            "xdebug\.remote_enable=.*",
            "xdebug\.idekey=.*",
            "xdebug\.remote_autostart=.*",
            "xdebug\.remote_port=.*",
        ];

        foreach ($xdebugRegexLines as $key => $xdebugRegexLine) {

            // Se on cavo i punti e virgola
            if ($toggle) {
                $iniContent = preg_replace("/;{$xdebugRegexLine}/", $xdebugLines[$key], $iniContent);
            }

            // Se li trovo, decido se cavare i punti e virgola o metterli
            if (preg_match("/{$xdebugRegexLine}/", $iniContent)) {
                if (!$toggle) {
                    $iniContent = preg_replace("/{$xdebugRegexLine}/", ";{$xdebugLines[$key]}", $iniContent);
                } else {
                    $iniContent = preg_replace("/{$xdebugRegexLine}/", $xdebugLines[$key], $iniContent);
                }
            } else { // Se non li trovo, li aggiungo se attivo
                if ($toggle) {
                    $iniContent .= "\n{$xdebugLines[$key]}";
                }
            }
        }
        if (!is_writable($inipath)) {
            die("Cannot write php.ini file");
        }
        $iniContent .= "\n";
        file_put_contents($inipath, $iniContent);

    }
}
