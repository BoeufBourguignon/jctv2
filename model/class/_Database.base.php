<?php


class Database
{
    private const HOST = '';
    private const PORT = '';
    private const CHARSET = 'utf8';
    private const LOGIN = '';
    private const PASS = '';
    private const DBNAME = '';
    private static ?PDO $connection = null;

    public static function GetConnection():PDO
    {
        if (self::$connection == null) {
            $dsn = 'mysql:host=' . self::HOST . ';port=' . self::PORT . ';dbname=' . self::DBNAME . ';charset=' . self::CHARSET;

            self::$connection = new PDO($dsn, self::LOGIN, self::PASS);
        }
        return self::$connection;
    }
}
