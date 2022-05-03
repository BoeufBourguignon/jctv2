<?php

class DifficultiesManager extends BaseManager
{
    public static function GetDifficulteById(int $idDifficulte)
    {
        self::getConnection();
        $query = "
            SELECT idDifficulte, libDifficulte
            FROM difficulte
            WHERE idDifficulte = :id
        ";
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Difficulty::class);
        $stmt->execute([":id" => $idDifficulte]);

        return $stmt->fetch();
    }

    public static function GetLesDifficultes(): array
    {
        self::getConnection();
        $query = "
            SELECT idDifficulte, libDifficulte
            FROM difficulte";
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Difficulty::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}