<?php

abstract class BaseManager
{
    protected static ?PDO $cnx = null;

    protected static function getConnection()
    {
        if(self::$cnx == null) {
            self::$cnx = Database::GetConnection();
        }
    }
}