<?php

namespace Cmhelp\Utils;

class LinuxVirtualHostManager extends VirtualHostManager
{
    protected $sitesAvailableDir = "/etc/apache2/sites-available/";
    protected $sitesEnabledDir = "/etc/apache2/sites-enabled/";

    /**
     * @param $name
     * @param $path
     */
    public function addVirtualHost($name, $path)
    {
        parent::addVirtualHost($name);
        $vhostContent = $this->generateVirtualHostContent($name, $path);

        $vhostConfigPath = "{$this->sitesAvailableDir}{$name}.conf";
        file_put_contents($vhostConfigPath, $vhostContent);

        $vhostProdConfigPath = "{$this->sitesEnabledDir}{$name}.conf";
        if (!file_exists($vhostProdConfigPath)) {
            symlink($vhostConfigPath, $vhostProdConfigPath);
        }
    }

    /**
     * @inheritDoc
     */
    protected function generateVirtualHostContent($name, $path)
    {
        return "<VirtualHost *:80>\n\tServerName {$name}\n\tServerAdmin webmaster@localhost\n\tDocumentRoot {$path}\n\tErrorLog $" . "{APACHE_LOG_DIR}/{$name}_error.log\n\tCustomLog $" . "{APACHE_LOG_DIR}/{$name}_access.log combined\n</VirtualHost>\n# vim: syntax=apache ts=4 sw=4 sts=4 sr noet";
    }

}