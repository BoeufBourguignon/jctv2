<?php
require_once(_CLASS . "/Categorie.php");


class CategorieManager extends BaseManager
{
    public function GetLesCategories():array
    {
        $query = "
            SELECT libCateg, refCateg, refParent
            FROM categorie
            WHERE refParent IS NULL";
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Categorie::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function GetSousCategories(string $refCateg):array
    {
        $query = "
            SELECT refCateg, libCateg, refParent
            FROM categorie
            WHERE refParent = :refParent
        ";
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Categorie::class);
        $stmt->execute([":refParent" => $refCateg]);

        return $stmt->fetchAll();
    }
}