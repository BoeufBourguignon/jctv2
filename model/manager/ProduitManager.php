<?php

class ProduitManager extends BaseManager
{
    /**
     * @param string $refProduit
     * @return Produit|bool
     */
    public static function GetProduit(string $refProduit): Produit|bool
    {
        self::getConnection();
        $query = "
            SELECT refProduit, imgPath, libProduit, descProduit, refCateg, refSousCateg, prix, idDifficulte, qteStock, seuilAlerte
            FROM v_produits p
            WHERE refProduit = :r
        ";
        $stmt = self::$cnx->prepare($query);
        $stmt->bindParam(":r", $refProduit);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param string $refCateg
     * @param array|null $triSousCategs
     * @param array|null $triDifficulte
     * @param string|null $triOrder
     * @param string|null $triWay
     * @return Produit[]
     */
    public static function GetProduitsByCateg(
        string $refCateg,
        array $triSousCategs = null,
        array $triDifficulte = null,
        ?string $triOrder = "refProduit",
        ?string $triWay = "ASC"
    ): array
    {
        self::getConnection();
        $triOrder = $triOrder == null ? $triOrder : "refProduit";
        $triWay = $triWay == null ? $triWay : "ASC";
        $query = "
            SELECT refProduit, refCateg, refSousCateg, imgPath, libProduit, descProduit, prix, idDifficulte, 
                   seuilAlerte, qteStock
            FROM v_produits
            WHERE refCateg = :categ
        ";
        if($triSousCategs != null) {
            $query .= "AND refSousCateg IN ('" . implode("', '", $triSousCategs) . "')\n";
        }
        if($triDifficulte != null) {
            $query .= "AND idDifficulte IN ('" . implode("', '", $triDifficulte) . "')\n";
        }
        if(in_array($triWay, ["ASC", "DESC"]) && in_array($triOrder, ["refProduit", "prix", "idDifficulte"])) {
            $query .= "ORDER BY " . $triOrder . " " . $triWay;
        }
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute([":categ" => $refCateg]);

        return $stmt->fetchAll();
    }

    /**
     * @param int $limit
     * @return Produit[]
     */
    public static function GetMostBoughtProduits(int $limit): array
    {
        self::getConnection();
        $query = "
            SELECT p.refProduit, qteTotale, imgPath, libProduit, descProduit, refCateg, refSousCateg, prix, idDifficulte, qteStock, seuilAlerte
            FROM v_most_bought_products v
                JOIN v_produits p ON p.refProduit = v.refProduit
            ORDER BY qteTotale DESC
            LIMIT " . $limit
        ;
        $stmt = self::$cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param string $r
     * @return bool|Produit[]
     */
    public static function SearchProduits(string $r): bool|array
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            SELECT refProduit, refCateg, refSousCateg, imgPath, libProduit, descProduit, prix, idDifficulte, 
                   seuilAlerte, qteStock
            FROM v_produits
            WHERE refCateg LIKE :r
            OR descProduit LIKE :r
        ");
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute([":r" => "%" . $r . "%"]);

        return $stmt->fetchAll();
    }

    /**
     * @return Produit[]
     */
    public static function GetAllProduits(): array
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            SELECT refProduit, refCateg, refSousCateg, imgPath, libProduit, descProduit, prix, idDifficulte,
                   seuilAlerte, qteStock
            FROM v_produits
        ");
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function SaveProduit(Produit $p): bool
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            UPDATE produit
            SET refCateg = :refCateg,
                libProduit = :lib,
                descProduit = :desc,
                prix = :prix,
                idDifficulte = :difficulte,
                qteStock = :qteStock,
                seuilAlerte = :seuil
            WHERE refProduit = :ref
        ");
        if($p->GetRefSousCateg() === null) {
            $stmt->bindValue(":refCateg", $p->GetRefCateg());
        } else {
            $stmt->bindValue(":refCateg", $p->GetRefSousCateg());
        }
        $stmt->bindValue(":lib", $p->GetLibProduit());
        $stmt->bindValue(":desc", $p->GetDescProduit(false));
        $stmt->bindValue(":prix", $p->GetPrix());
        $stmt->bindValue(":difficulte", $p->GetIdDifficulte());
        $stmt->bindValue(":qteStock", $p->GetQteStock());
        $stmt->bindValue(":seuil", $p->GetSeuilAlerte());
        $stmt->bindValue(":ref", $p->GetRefProduit());
        return $stmt->execute();
    }

    public static function AddProduit(Produit $p)
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            INSERT INTO produit (refProduit, libProduit, descProduit, refCateg, prix, idDifficulte, qteStock, seuilAlerte)
            VALUES (:ref, :lib, :desc, :categ, :prix, :difficulte, :qteStock, :seuil)
        ");
        if($p->GetRefSousCateg() === null) {
            $stmt->bindValue(":categ", $p->GetRefCateg());
        } else {
            $stmt->bindValue(":categ", $p->GetRefSousCateg());
        }
        $stmt->bindValue(":lib", $p->GetLibProduit());
        $stmt->bindValue(":desc", $p->GetDescProduit(false));
        $stmt->bindValue(":prix", $p->GetPrix());
        $stmt->bindValue(":difficulte", $p->GetIdDifficulte());
        $stmt->bindValue(":qteStock", $p->GetQteStock());
        $stmt->bindValue(":seuil", $p->GetSeuilAlerte());
        $stmt->bindValue(":ref", $p->GetRefProduit());
        return $stmt->execute();
    }

    public static function DeleteProduit(string $ref): bool
    {
        self::getConnection();
        //Si le produit a déjà été commande, il ne peut pas être supprimé
        $stmtExiste = self::$cnx->prepare("
            SELECT count(*) FROM lignecommande WHERE refProduit = :ref
        ");
        $stmtExiste->bindParam(":ref", $ref);
        $stmtExiste->execute();
        $nbCommandes = $stmtExiste->fetchColumn();
        $peutSupprimer = $nbCommandes == 0;
        if($peutSupprimer) {
            $stmt = self::$cnx->prepare("
                DELETE FROM produit
                WHERE refProduit = :ref
            ");
            $stmt->bindParam(":ref", $ref);
            $peutSupprimer = $stmt->execute();
            $stmt->debugDumpParams();
        }
        return $peutSupprimer;
    }

    public static function GetQteCommandee(string $ref): int
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("CALL quantiteCommandee(:ref)");
        $stmt->bindParam(":ref", $ref);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}