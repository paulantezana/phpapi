<?php

class Database
{
    private $connection;
    public function __construct()
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $dbname = APP_ENV['DDBB_NAME'];
        $user = APP_ENV['DDBB_USER'];
        $password = APP_ENV['DDBB_PASSWORD'];
        $host = APP_ENV['DDBB_HOST'];

        $this->connection = new PDO("mysql:host={$host};dbname={$dbname}", $user, $password, $options);

        $this->connection->exec("SET CHARACTER SET UTF8");
    }

    public function getConnection()
    {
        return $this->connection;
    }
    
    public function closeConnection()
    {
        $this->connection = null;
    }
}
