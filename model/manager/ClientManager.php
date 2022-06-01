<?php
require_once(_CLASS . "/Client.php");


class ClientManager extends BaseManager
{
    public static function GetLesClients(): bool|array
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            SELECT idClient, loginClient, passwordClient, mailClient, idRoleClient
            FROM client
        ");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);
        return $stmt->fetchAll();
    }

    public static function ChangerPassword(int $idClient, string $pwd): bool
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            UPDATE client
            SET passwordClient = :pwd
            WHERE idClient = :id
        ");
        $stmt->bindParam(":id", $idClient);
        $stmt->bindValue(":pwd", password_hash($pwd, PASSWORD_BCRYPT));
        return $stmt->execute();
    }

    public static function ChangerInfos(int $idClient, string $login, string $mail): bool
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            UPDATE client
            SET loginClient = :login,
                mailClient = :mail
            WHERE idClient = :idC
        ");
        $stmt->bindParam(":login", $login);
        $stmt->bindParam(":mail", $mail);
        $stmt->bindParam(":idC", $idClient);
        return $stmt->execute();
    }

    public static function CanConnect(string $cid, string $cuid): bool
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT * FROM user_connection
            WHERE idConnection = :cid AND idClient = :cuid
        ";
        $stmt = $cnx->prepare($query);
        $stmt->execute([":cid" => $cid, "cuid" => $cuid]);

        return !($stmt->fetchColumn() == false);
    }

    public static function DoConnect(Client $client)
    {
        $cid = md5(uniqid());

        $stmt = self::$cnx->prepare("
            INSERT INTO user_connection (idClient, idConnection) VALUES
            (:cuid, :cid)
        ");
        $stmt->execute([":cuid" => $client->GetId(), ":cid" => $cid]);

        //Ajout de la connexion en cookie
        setcookie("cid", $cid, time() + 365*24*60*60, "/");
        setcookie("cuid", $client->GetId(), time() + 365*24*60*60, "/");
    }

    public static function DoLogout()
    {
        self::getConnection();

        $cid = $_COOKIE["cid"] ?? null;
        $cuid = $_COOKIE["cuid"] ?? null;

        if($cid != null && $cuid != null) {
            $stmt = self::$cnx->prepare("
                DELETE FROM user_connection
                WHERE idClient = :cuid
                AND idConnection = :cid
            ");
            $stmt->execute([":cid" => $cid, ":cuid" => $cuid]);
        }

        setcookie("cid", "", time() - 1800, "/");
        setcookie("cuid", "", time() - 1800, "/");
    }

    public static function TryLeClient(string $login): Client|false
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            SELECT idClient, loginClient, passwordClient, mailClient, idRoleClient
            FROM client
            WHERE loginClient = :l
        ");
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Client");
        $stmt->execute([":l" => $login]);

        return $stmt->fetch();
    }

    public static function GetHistorique(Client $client): array
    {
        self::getConnection();
        $historique = array();
        $stmtCommandes = self::$cnx->prepare("
            SELECT idCommande 
            FROM commande c 
            WHERE idClient = :client
            AND NOT EXISTS (SELECT * FROM v_panier p WHERE p.idCommande = c.idCommande)
        ");
        $stmtCommandes->execute([":client" => $client->GetId()]);
        while($idCommande = $stmtCommandes->fetch(PDO::FETCH_COLUMN)) {
            $stmtDateCommande = self::$cnx->prepare("
                SELECT date
                FROM suivietatcommande
                WHERE idCommande = :commande
                AND idEtatCommande = 2
            ");
            $stmtDateCommande->execute([":commande" => $idCommande]);
            $date = $stmtDateCommande->fetchColumn();
            $stmtDetails = self::$cnx->prepare("
                SELECT p.refProduit, qte, libProduit, prix
                FROM lignecommande lc
                    JOIN produit p on lc.refProduit = p.refProduit
                WHERE idCommande = :commande
            ");
            $stmtDetails->setFetchMode(PDO::FETCH_KEY_PAIR);
            $stmtDetails->execute([":commande" => $idCommande]);

            $historique[$idCommande] = [
                "date" => $date,
                "produits" => $stmtDetails->fetchAll(PDO::FETCH_ASSOC)
            ];
        }
        return $historique;
    }



    public static function AddLeClient(string $login, string $password, string $mail)
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            INSERT INTO client (loginClient, passwordClient, mailClient, idRoleClient)
            VALUES (:l, :p, :m, 1)
        ");
        $stmt->execute([
            ":l" => $login,
            ":p" => password_hash($password, PASSWORD_BCRYPT),
            ":m" => $mail
        ]);

        return $stmt->fetch();
    }

    /**
     * @param string $login
     * @return bool True si existe, sinon False
     */
    public static function UserLoginExists(string $login):bool
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            SELECT idClient
            FROM client
            WHERE loginClient = :l
        ");
        $stmt->execute([":l" => $login]);

        return $stmt->rowCount() > 0;
    }

    /**
     * @param string $mail
     * @return bool True si exists, sinon False
     */
    public static function UserMailExists(string $mail):bool
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            SELECT idClient
            FROM client
            WHERE mailClient = :m
        ");
        $stmt->execute([":m" => $mail]);

        return $stmt->rowCount() > 0;
    }

    public static function GetUserById(int $id):Client|bool
    {
        self::getConnection();
        $stmt = self::$cnx->prepare("
            SELECT idClient, loginClient, passwordClient, mailClient, idRoleClient
            FROM client
            WHERE idClient = :id
        ");
        $stmt->setFetchMode(PDO::FETCH_CLASS, Client::class);
        $stmt->execute([":id" => $id]);

        return $stmt->fetch();
    }
}