<?php

namespace App\Services;

use PDO;

readonly class Database
{
    public PDO $pdo;

    public function __construct(Config $config)
    {
        $connectionString = sprintf(
            'mysql:host=%s;dbname=%s',
            $config['DB_HOST'],
            $config['DB_NAME'],
        );

        $this->pdo = new PDO(
            $connectionString,
            $config['DB_USER'],
            $config['DB_PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
}
