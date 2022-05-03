<?php
require_once(_CLASS . "/Categorie.php");


class CategorieManager extends BaseManager
{
    public static function GetCategorie(string $ref)
    {
        self::getConnection();
        $query = "
            SELECT refCateg, libCateg, refParent
            FROM categorie
            WHERE refCateg = :ref
        ";
        $stmt = self::$cnx->prepare($query);
        $stmt->execute([":ref" => $ref]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Categorie::class);
        return $stmt->fetch();
    }

    public static function GetLesCategories():array
    {
        self::getConnection();
        $query = "
            SELECT libCateg, refCateg, refParent
            FROM categorie
            WHERE refParent IS NULL";
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Categorie::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function GetSousCategories(string $refCateg):array
    {
        self::getConnection();
        $query = "
            SELECT refCateg, libCateg, refParent
            FROM categorie
            WHERE refParent = :refParent
        ";
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Categorie::class);
        $stmt->execute([":refParent" => $refCateg]);

        return $stmt->fetchAll();
    }
}