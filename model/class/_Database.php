<?php


class Database
{
    private const HOST = '127.0.0.1';
    private const PORT = '3307';
    private const CHARSET = 'utf8';
    private const LOGIN = 'AppJCT';
    private const PASS = 'sk4#Srvmpcrci';
    private const DBNAME = 'jeancassetete';
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
