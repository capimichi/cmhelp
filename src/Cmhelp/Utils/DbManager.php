<?php

namespace Cmhelp\Utils;

class DbManager
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * DbManager constructor.
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param $name
     * @param bool $force
     * @throws \Exception
     */
    public function createDatabase($name, $force = false)
    {
        $conn = new \mysqli($this->host, $this->user, $this->password);
        if ($conn->connect_error) {
            throw new \Exception("Impossibile connetersi al database: " . $conn->connect_error);
        }
        if ($force) {
            $dropQuery = "DROP DATABASE {$name}";
            $conn->query($dropQuery);
        }
        $createQuery = "CREATE DATABASE {$name}";
        $conn->query($createQuery);
        $conn->close();
    }


    public function importDatabaseFromFile($name, $file)
    {
        $conn = new \mysqli($this->host, $this->user, $this->password, $name);
        $templine = '';
        $lines = file($file);
        foreach ($lines as $line) {
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }
            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $conn->query($templine);
                $templine = '';
            }
        }
        $conn->close();
    }

}