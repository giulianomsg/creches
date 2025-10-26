<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dbConfig = require __DIR__ . '/../../config/database.php';

            try {
                self::$connection = new PDO(
                    $dbConfig['dsn'],
                    $dbConfig['username'],
                    $dbConfig['password'],
                    $dbConfig['options']
                );
            } catch (PDOException $exception) {
                throw new PDOException('Erro ao conectar ao banco de dados: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
            }
        }

        return self::$connection;
    }
}
