<?php
namespace App\Core;
use PDO;
class Connection
{
    private static $conn = null;

    public static function getInstance()
    {
        if (!self::$conn) {
            $config = require __DIR__ . '/../Helpers/config.php';

            $db = $config['db'];

            self::$conn = new PDO(
                "mysql:host={$db['host']};dbname={$db['name']};charset=utf8",
                $db['user'],
                $db['pass']
            );

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}