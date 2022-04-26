<?php

class ProduitManager extends BaseManager
{
    public function GetRandomProduit(): Produit
    {
        $query = "
            SELECT refProduit, imgPath, libProduit, descProduit, refCateg, prix, idDifficulte, qteStock, seuilAlerte
            FROM produit
            ORDER BY RAND()
            LIMIT 1
        ";
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute();
        $produit = $stmt->fetch();

        $produit->SetDifficulte($this->DifficultiesManager()->GetDifficulteById($produit->GetIdDifficulte()));

        return $produit;
    }

    public function GetProduitsByCateg(string $refCateg, string $order = "prix", string $way = "ASC", array $subcategs = null, array $difficulties = null):array
    {
        $query = "
            SELECT refProduit, refCateg, refSousCateg, imgPath, libProduit, descProduit, prix, idDifficulte, seuilAlerte, qteStock
            FROM v_produits
            WHERE refCateg = :refCateg
        ";
        if($subcategs != null && count($subcategs) > 0) {
            $query .= "
                AND " . implode(",", $subcategs) . "
            ";
        }
        if($difficulties != null && count($difficulties) > 0) {
            $query .= "
                AND " . implode(",", $difficulties) . "
            ";
        }
        $query .= "
                ORDER BY " . $order . " " . $way . "
            ";
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute([":refCateg" => $refCateg]);

        $lesProduits = array();
        /** @var Produit $unProduit */
        while($unProduit = $stmt->fetch()) {
            $unProduit->SetDifficulte($this->DifficultiesManager()->GetDifficulteById($unProduit->GetIdDifficulte()));
            $lesProduits[] = $unProduit;
        }

        return $lesProduits;

//        if ($difficulties !== null) {
//            $whereDifficulty =
//                "AND idDifficulte IN (".implode(",",$difficulties).")\n";
//        }
//        if ($subcategs === null) {
//            $subquery =
//                "SELECT *\n".
//                "FROM categorie c\n".
//                "WHERE idParent = :idCateg\n".
//                "AND c.idCateg = p.idCateg";
//            $union =
//                "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n" .
//                "FROM produit p\n" .
//                "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
//                "WHERE idCateg = :idCateg\n";
//            if (isset($whereDifficulty)) {
//                $union .= $whereDifficulty;
//            }
//            $query =
//                "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n" .
//                "FROM produit p\n" .
//                "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
//                "WHERE EXISTS (" . $subquery . ")\n";
//            if (isset($whereDifficulty)) {
//                $query .= $whereDifficulty;
//            }
//            $query .=
//                "UNION\n" .
//                $union .
//                "ORDER BY $order $way";
//
//            $stmt = $cnx->prepare($query);
//            $stmt->bindParam(":idCateg", $idCateg, PDO::PARAM_INT);
//        }
//        else {
//            $query =
//                "SELECT idProduit, refProduit, imgPath, libProduit, descProduit, idCateg, prix, d.idDifficulte, libDifficulte\n" .
//                "FROM produit p\n" .
//                "    JOIN difficulte d ON d.idDifficulte = p.difficulte\n".
//                "WHERE idCateg IN (".implode(",",$subcategs).")\n";
//            if (isset($whereDifficulty)) {
//                $query .= $whereDifficulty;
//            }
//            $query .=
//                "ORDER BY $order $way";
//
//            $stmt = $cnx->prepare($query);
//        }
//        $stmt->execute();
//
//        $arrObjProduits = array();
//        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $produit)
//        {
//            $arrObjProduits[] = self::toObj($produit);
//        }
//        return $arrObjProduits;
    }

    public function GetProduitByRef(string $refProduit): Produit
    {
        $query = "
            SELECT refProduit, imgPath, libProduit, descProduit, refCateg, prix, idDifficulte, qteStock, seuilAlerte
            FROM produit p
            WHERE refProduit = :r
        ";
        $stmt = $this->cnx->prepare($query);
        $stmt->bindParam(":r", $refProduit);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute();

        /** @var Produit $produit */
        $produit = $stmt->fetch();
        $produit->SetDifficulte($this->DifficultiesManager()->GetDifficulteById($produit->GetIdDifficulte()));

        return $produit;
    }

    public function GetProduitsBySearch(string $r): array
    {
        $query = "
            SELECT refProduit, imgPath, libProduit, descProduit, refCateg, prix, idDifficulte, qteStock, seuilAlerte
            FROM produit
            WHERE libProduit LIKE :r
            OR descProduit LIKE :r
        ";
        $stmt = $this->cnx->prepare($query);
        $stmt->bindValue(":r", "%" . $r . "%");
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute();

        $produits = array();

        /** @var Produit $produit */
        foreach($stmt->fetchAll() as $produit) {
            $produit->SetDifficulte($this->DifficultiesManager()->GetDifficulteById($produit->GetIdDifficulte()));
            $produits[] = $produit;
        }

        return $produits;
    }

    public function GetMostBoughtProduits(int $limit): array
    {
        $query = "
            SELECT p.refProduit, qteTotale, imgPath, libProduit, descProduit, refCateg, prix, idDifficulte, qteStock, seuilAlerte
            FROM v_most_bought_products v
                JOIN produit p ON p.refProduit = v.refProduit
            ORDER BY qteTotale DESC
            LIMIT " . $limit
        ;
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute();

        $produits = array();
        while($produit = $stmt->fetch()) {
           $produits[] = $produit->SetDifficulte($this->DifficultiesManager()->GetDifficulteById($produit->GetIdDifficulte()));
        }

        return $produits;
    }
}