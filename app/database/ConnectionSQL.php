<?php

namespace app\database;

use PDO;

class ConnectionSQL {
    private static $connect = null;

    public static function connect() {
        if (!self::$connect) {
            self::$connect = new PDO(
                "mysql:host=localhost;dbname=trevo_general",
                "root", 
                "",
                [
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC
                ]
            );
        }

        return self::$connect;
    }
}
