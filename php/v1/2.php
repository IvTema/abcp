<?php

namespace Gateway;

require_once 'dbConnection.php';

use PDO;

class User
{
    /**
     * Возвращает список пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    public static function getUsers(int $ageFrom): array
    {
        $stmt = \DbService\DatabaseConnection::getInstance()->prepare(
            "SELECT id, name, lastName, `from`, age, settings " .
            "FROM Users WHERE age > :ageFrom LIMIT " .
            \Manager\User::limit
        );
        $stmt->execute([':ageFrom' => $ageFrom]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
            $settings = json_decode($row['settings']);
            $users[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'lastName' => $row['lastName'],
                'from' => $row['from'],
                'age' => $row['age'],
                'key' => $settings['key'],
            ];
        }

        return $users;
    }

    /**
     * Возвращает пользователя по имени.
     * @param string $name
     * @return array
     */
    public static function user(string $name): array
    {
        $stmt = \DbService\DatabaseConnection::getInstance()->prepare(
            "SELECT id, name, lastName, `from`, age, settings " .
            "FROM Users WHERE name = :name"
        );
        $stmt->execute([':name' => $name]);
        $user_by_name = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'id' => $user_by_name['id'],
            'name' => $user_by_name['name'],
            'lastName' => $user_by_name['lastName'],
            'from' => $user_by_name['from'],
            'age' => $user_by_name['age'],
        ];
    }

    /**
     * Добавляет пользователя в базу данных.
     * @param string $name
     * @param string $lastName
     * @param int $age
     * @return string
     */
    public static function add(string $name, string $lastName, int $age): int
    {
        try {
            $sth = \DbService\DatabaseConnection::getInstance()->prepare(
                "INSERT INTO Users (name, lastName, age) " .
                "VALUES (:name, :age, :lastName)"
            );
            $sth->execute([':name' => $name, ':age' => $age, ':lastName' => $lastName]);

            return (int)self::getInstance()->lastInsertId();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}