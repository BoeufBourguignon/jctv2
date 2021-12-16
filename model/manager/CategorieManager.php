<?php

class CategorieManager
{
    public static function GetLesCategories():array
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idCateg, libCateg, refCateg \n
            FROM categorie \n
            WHERE idParent IS NULL";
        $stmt = $cnx->prepare($query);
        $stmt->execute();


        return self::toObj($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function GetSousCategories(int $idCateg):array
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idCateg, libCateg, refCateg \n
            FROM categorie \n
            WHERE idParent = :idParent";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":idParent", $idCateg, PDO::PARAM_INT);
        $stmt->execute();


        return self::toObj($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function GetIdByRef(string $refCateg):int
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idCateg \n
            FROM categorie \n
            WHERE refCateg = :ref";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":ref", $refCateg);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function toObj(array $array):array
    {
        $toRet = array();
        foreach ($array as $categ)
        {
            $categToAdd = new Categorie();
            $categToAdd->SetId($categ["idCateg"]);
            $categToAdd->SetLibelle($categ["libCateg"]);
            $categToAdd->SetRef($categ["refCateg"]);

            $toRet[] = $categToAdd;
        }
        return $toRet;
    }
}