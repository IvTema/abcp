<?php

namespace Manager;

require_once 'dbConnection.php';
require_once '2.php';

class UserManager
{
    const limit = 10;

    /**
     * Возвращает пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    function getUsers(int $ageFrom): array
    {
        $ageFrom = (int)trim($ageFrom);

        return \Gateway\User::getUsers($ageFrom);
    }

    /**
     * Возвращает пользователей по списку имен.
     * @return array
     */
    public static function getByNames(): array
    {
        if (!isset($_GET['names']) || !is_array($_GET['names'])) {
            throw new \InvalidArgumentException("'names' should be an array");
        }

        $names = $_GET['names'];
        foreach ($names as $key => $name) {
            $names[$key] = filter_var($name, FILTER_SANITIZE_STRING);
        }
        $placeholders = str_repeat('?,', count($names) - 1) . '?';
        $stmt = \DbService\DatabaseConnection::getInstance()->prepare("SELECT * FROM Users WHERE name IN ($placeholders)");
        $stmt->execute($names);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $users;
    }

    /**
     * Добавляет пользователей в базу данных.
     * @param $users
     * @return array
     */
    public function users($users): array
    {
        $ids = [];
        $db = \DbService\DatabaseConnection::getInstance();
        $db->beginTransaction();
        try {
            foreach ($users as $user) {
                \Gateway\User::add($user['name'], $user['lastName'], $user['age']);
                $ids[] = $db->lastInsertId();
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            error_log($e->getMessage());
            return ['error' => $e->getMessage()];
        }

        return $ids;
    }
}