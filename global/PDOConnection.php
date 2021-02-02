<?php
class PDOConnection {
    private static $dbhost = 'localhost';
    private static $dbname = 'mvcpeldablog';
    private static $dbuser = 'root';
    private static $dbpass = '';
    private static $db_singleton = null;

    public static function getPeldany() {
        if(self::$db_singleton == null) {
            self::$db_singleton = new PDO("mysql:host=" .self::$dbhost. ";dbname=" . self::$dbname . ";charset=utf8", self::$dbuser, self::$dbpass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        return self::$db_singleton;
    }
}
