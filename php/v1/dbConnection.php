<?php

namespace DbService;

use PDO;

class DatabaseConnection
{
    /**
     * @var PDO
     */
    private static $instance;

    /**
     * Реализация singleton
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (is_null(self::$instance)) {
            $dsn = 'mysql:dbname=db;host=127.0.0.1';
            $user = 'dbuser';
            $password = 'dbpass';
            try {
                self::$instance = new PDO($dsn, $user, $password);
            } catch (PDOException $e) {
                error_log($e->getMessage());
                throw new \Exception('Ошибка подключения к базе данных: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}