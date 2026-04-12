<?php
namespace App\Core;
use PDO;
class Connection
{
    private static $conn = null;

    public static function getInstance()
    {
        if (!self::$conn) {
            $dbhost = 'localhost';
            $dbname = 'Tamghrabit';
            $dbuser = 'root';
            $dbpass = '';
            self::$conn = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}