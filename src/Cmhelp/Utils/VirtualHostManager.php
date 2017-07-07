<?php

namespace Cmhelp\Utils;

abstract class VirtualHostManager
{

    protected $sitesAvailableDir;
    protected $sitesEnabledDir;

    
    public function addVirtualHost($name, $path)
    {
        if (!is_writable($this->sitesAvailableDir) || !is_writable($this->sitesEnabledDir)) {
            throw new \Exception("Cannot write virtual host configuration");
        }
    }

    /**
     * @param $host
     * @param $name
     * @throws \Exception
     */
    public function addHostname($host, $name)
    {
        $hostsPath = "/etc/hosts";
        if (!is_readable($hostsPath) || !is_writable($hostsPath)) {
            throw new \Exception("Cannot read/write {$hostsPath}");
        }
        $hostsContent = file_get_contents($hostsPath);
        $hostLine = str_replace("localhost", "127.0.0.1", $host) . "\t" . $name;
        if (!preg_match("/{$hostLine}/is", $hostsContent)) {
            $hostsContent .= "\n{$hostLine}";
        }
        file_put_contents($hostsPath, $hostsContent);
    }

    /**
     * @param $name
     * @param $path
     * @return string
     */
    protected abstract function generateVirtualHostContent($name, $path);
}