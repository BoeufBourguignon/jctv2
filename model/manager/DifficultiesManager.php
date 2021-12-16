<?php

class DifficultiesManager
{
    public static function GetLesDifficultes():array
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idDifficulte, libDifficulte \n
            FROM difficulte";
        $stmt = $cnx->prepare($query);
        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_CLASS, "Difficulty");
    }

    public static function GetDifficultyLibelle(int $idDifficulty) {
        $cnx = Database::GetConnection();
        $query = "
            SELECT libDifficulte \n
            FROM difficulte \n
            WHERE idDifficulte = :difficulte";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":difficulte", $idDifficulty, PDO::PARAM_INT);
        $stmt->execute();


        return $stmt->fetch(PDO::FETCH_DEFAULT)[0];
    }
}