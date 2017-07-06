<?php

namespace Cmhelp\Utils;

class LinuxVirtualHostManager extends VirtualHostManager
{
    protected $sitesAvailableDir = "/etc/apache2/sites-available/";
    protected $sitesEnabledDir = "/etc/apache2/sites-enabled/";

    /**
     * @param $name
     */
    public function addVirtualHost($name)
    {
        parent::addVirtualHost($name);
        $vhostContent = $this->generateVirtualHostContent($name);

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
    protected function generateVirtualHostContent($name)
    {
        return "<VirtualHost *:80>\n\tServerName {$name}\n\tServerAdmin webmaster@localhost\n\tDocumentRoot {$name}\n\tErrorLog $" . "{APACHE_LOG_DIR}/{$name}_error.log\n\tCustomLog $" . "{APACHE_LOG_DIR}/{$name}_access.log combined\n</VirtualHost>\n# vim: syntax=apache ts=4 sw=4 sts=4 sr noet";
    }

}