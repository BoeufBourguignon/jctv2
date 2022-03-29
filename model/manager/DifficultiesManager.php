<?php

class DifficultiesManager extends BaseManager
{
    public function GetDifficulteById(int $idDifficulte)
    {
        $query = "
            SELECT idDifficulte, libDifficulte
            FROM difficulte
            WHERE idDifficulte = :id
        ";
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Difficulty::class);
        $stmt->execute([":id" => $idDifficulte]);

        return $stmt->fetch();
    }

    public function GetLesDifficultes(): array
    {
        $query = "
            SELECT idDifficulte, libDifficulte
            FROM difficulte";
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Difficulty::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}