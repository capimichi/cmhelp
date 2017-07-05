<?php

namespace Cmhelp\Utils;

class MageDbManager extends DbManager
{


    public function changeBaseUrl($database, $url)
    {
        $conn = new \mysqli($this->host, $this->user, $this->password, $database);
        if ($conn->connect_error) {
            throw new \Exception("Impossibile connetersi al database: " . $conn->connect_error);
        }
        $queryUnsecureUrl = "update core_config_data set value = '{$url}' where path = 'web/unsecure/base_url'; ";
        $querySecureUrl = "update core_config_data set value = '{$url}' where path = 'web/secure/base_url';";
        $conn->query($queryUnsecureUrl);
        $conn->query($querySecureUrl);
        $conn->close();
    }
}