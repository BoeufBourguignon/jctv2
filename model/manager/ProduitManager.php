<?php

class ProduitManager
{
    public static function GetProduitsByCateg(int $idCateg, string $order = "prix", string $way = "ASC", array $subcategs = null, array $difficulties = null):array
    {
        $cnx = Database::GetConnection();
        if ($difficulties !== null) {
            $whereDifficulty =
                "AND idDifficulte IN (".implode(",",$difficulties).")\n";
        }
        if ($subcategs === null) {
            $subquery =
                "SELECT *\n".
                "FROM categorie c\n".
                "WHERE idParent = :idCateg\n".
                "AND c.idCateg = p.idCateg";
            $union =
                "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n" .
                "FROM produit p\n" .
                "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
                "WHERE idCateg = :idCateg\n";
            if (isset($whereDifficulty)) {
                $union .= $whereDifficulty;
            }
            $query =
                "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n" .
                "FROM produit p\n" .
                "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
                "WHERE EXISTS (" . $subquery . ")\n";
            if (isset($whereDifficulty)) {
                $query .= $whereDifficulty;
            }
            $query .=
                "UNION\n" .
                $union .
                "ORDER BY $order $way";

            $stmt = $cnx->prepare($query);
            $stmt->bindParam(":idCateg", $idCateg, PDO::PARAM_INT);
        }
        else {
            $query =
                "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n" .
                "FROM produit p\n" .
                "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
                "WHERE idCateg IN (".implode(",",$subcategs).")\n";
            if (isset($whereDifficulty)) {
                $query .= $whereDifficulty;
            }
            $query .=
                "ORDER BY $order $way";

            $stmt = $cnx->prepare($query);
        }
        $stmt->execute();

        $arrObjProduits = array();
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $produit)
        {
            $arrObjProduits[] = self::toObj($produit);
        }
        return $arrObjProduits;
    }

    public static function GetRandomProduit(): Produit
    {
        $cnx = Database::GetConnection();
        $query =
            "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n".
            "FROM produit p\n".
            "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
            "ORDER BY RAND()\n".
            "LIMIT 1";

        $stmt = $cnx->prepare($query);
        $stmt->execute();


        return self::toObj($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public static function GetProduitsByRefCateg(string $refCateg): array
    {
        $cnx = Database::GetConnection();

        $subQuery =
            "SELECT *\n".
            "FROM categorie c2\n".
            "WHERE c2.refCateg = :ref\n".
            "AND c2.idCateg = c.idParent\n";
        $unionSubQuery =
            "SELECT *\n".
            "FROM categorie c\n".
            "WHERE refCateg = :ref\n".
            "AND c.idCateg = p.idCateg\n";
        $union =
            "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n".
            "FROM produit p\n".
            "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
            "WHERE EXISTS (".$unionSubQuery.")\n";
        $query =
            "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, c.idCateg, prix, d.idDifficulte, libDifficulte\n".
            "FROM produit p\n".
            "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
            "    JOIN categorie c on p.idCateg = c.idCateg\n".
            "WHERE EXISTS (".$subQuery.")\n".
            "UNION\n".
            $union;

        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":ref", $refCateg);
        $stmt->execute();

        $arrObjProduits = array();
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $produit)
        {
            $arrObjProduits[] = self::toObj($produit);
        }
        return $arrObjProduits;
    }

    public static function GetIdByRef(string $ref)
    {
        $cnx = Database::GetConnection();

        $query =
            "SELECT idProduit\n".
            "FROM produit\n".
            "WHERE refProduit = :ref";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":ref", $ref);
        $stmt->execute();

        return $stmt->fetch()["idProduit"];
    }

    public static function GetProduitByRef(string $ref):Produit
    {
        $cnx = Database::GetConnection();

        $query =
            "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n".
            "FROM produit p\n" .
            "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
            "WHERE refProduit = :r";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":r", $ref);
        $stmt->execute();

        return self::toObj($stmt->fetch());
    }

    public static function toObj(array $produit): Produit
    {
        $difficulteToAdd = new Difficulty();
        $difficulteToAdd->setId($produit["idDifficulte"]);
        $difficulteToAdd->setLibelle($produit["libDifficulte"]);

        $objProduit = new Produit();
        $objProduit->SetId($produit["idProduit"]);
        $objProduit->SetReference($produit["refProduit"]);
        $objProduit->SetImagePath($produit["imgPath"]);
        $objProduit->SetLibelle($produit["libProduit"]);
        $objProduit->SetDescription($produit["descProduit"]);
        $objProduit->SetCateg($produit["idCateg"]);
        $objProduit->SetPrix($produit["prix"]);
        $objProduit->SetDifficulte($difficulteToAdd);

        return $objProduit;
    }
}