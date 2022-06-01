<?php
require_once(_CLASS . "/Categorie.php");


class CategorieManager extends BaseManager
{
    public static function GetCategorie(string $ref): Categorie|bool
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

    public static function GetLesCategories(): array
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

    public static function GetSousCategories(string $refCateg): array
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

    public static function GetAllCategories(): array
    {
        self::getConnection();
        $query = "
            SELECT refCateg, libCateg, refParent
            FROM categorie
        ";
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Categorie::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function UpdateCategorie(Categorie $c): bool
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            UPDATE categorie
            SET libCateg = :lib,
                refParent = :parent
            WHERE refCateg = :ref
        ");
        $stmt->bindValue(":lib", $c->GetLibelle());
        $stmt->bindValue(":parent", $c->GetRefParent());
        $stmt->bindValue(":ref", $c->GetRef());
        return $stmt->execute();
    }

    public static function AddCategorie(Categorie $c)
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            INSERT INTO categorie (refCateg, libCateg, refParent) 
            VALUES (:ref, :lib, :parent)
        ");
        $stmt->bindValue(":lib", $c->GetLibelle());
        $stmt->bindValue(":parent", $c->GetRefParent());
        $stmt->bindValue(":ref", $c->GetRef());
        return $stmt->execute();
    }

    public static function DeleteCategorie(string $ref): bool
    {
        self::getConnection();
        //Si la catégorie a des produits, elle ne peut pas être supprimée
        $stmtExiste = self::$cnx->prepare("
            SELECT count(*) FROM v_produits WHERE refCateg = :ref or refSousCateg = :ref
        ");
        $stmtExiste->bindParam(":ref", $ref);
        $stmtExiste->execute();
        $nbCommandes = $stmtExiste->fetchColumn();
        $peutSupprimer = $nbCommandes == 0;
        if($peutSupprimer) {
            $stmt = self::$cnx->prepare("
                DELETE FROM categorie
                WHERE refCateg = :ref
            ");
            $stmt->bindParam(":ref", $ref);
            $peutSupprimer = $stmt->execute();
        }
        return $peutSupprimer;
    }
}