<?php


class Database
{
    private const HOST = '127.0.0.1';
    private const PORT = '3306';
    private const CHARSET = 'utf8';
    private const LOGIN = 'u934752231_AppCasseTete';
    private const PASS = 'sk4#Srvmpcrci';
    private const DBNAME = 'u934752231_jeancassetete';
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
