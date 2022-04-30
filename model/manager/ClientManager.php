<?php
require_once(_CLASS . "/Client.php");


class ClientManager extends BaseManager
{
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

    public function DoConnect(Client $client)
    {
        $cid = md5(uniqid());

        $query = "
            INSERT INTO user_connection (idClient, idConnection) VALUES
            (:cuid, :cid)
        ";
        $stmt = $this->cnx->prepare($query);
        $stmt->execute([":cuid" => $client->GetId(), ":cid" => $cid]);

        //Ajout de la connexion en cookie
        setcookie("cid", $cid, time() + 365*24*60*60, "/");
        setcookie("cuid", $client->GetId(), time() + 365*24*60*60, "/");
    }

    public function DoLogout()
    {
        $cid = $_COOKIE["cid"] ?? null;
        $cuid = $_COOKIE["cuid"] ?? null;

        if($cid != null && $cuid != null) {
            $query = "
                DELETE FROM user_connection
                WHERE idClient = :cuid
                AND idConnection = :cid
            ";
            $stmt = $this->cnx->prepare($query);
            $stmt->execute([":cid" => $cid, ":cuid" => $cuid]);
        }

        setcookie("cid", "", time() - 1800, "/");
        setcookie("cuid", "", time() - 1800, "/");
    }

    public function TryLeClient(string $login): Client|false
    {
        $query = "
            SELECT idClient, loginClient, passwordClient, mailClient, idRoleClient
            FROM client
            WHERE loginClient = :l
        ";
        $stmt = $this->cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Client");
        $stmt->execute([":l" => $login]);

        return $stmt->fetch();
    }

    public function GetHistorique(Client $client): array
    {
        $historique = array();
        $queryCommandes = "
            SELECT idCommande 
            FROM commande c 
            WHERE idClient = :client
            AND NOT EXISTS (SELECT * FROM v_panier p WHERE p.idCommande = c.idCommande)
        ";
        $stmtCommandes = $this->cnx->prepare($queryCommandes);
        $stmtCommandes->execute([":client" => $client->GetId()]);
        $commandes = $stmtCommandes->fetchAll(PDO::FETCH_COLUMN);
        foreach($commandes as $idCommande) {
            $queryDateCommande = "
                SELECT date
                FROM suivietatcommande
                WHERE idCommande = :commande
                AND idEtatCommande = 2
            ";
            $stmtDateCommande = $this->cnx->prepare($queryDateCommande);
            $stmtDateCommande->execute([":commande" => $idCommande]);
            $date = $stmtDateCommande->fetchColumn();
            $queryDetails = "
                SELECT p.refProduit, qte, libProduit, prix
                FROM lignecommande lc
                    JOIN produit p on lc.refProduit = p.refProduit
                WHERE idCommande = :commande
            ";
            $stmtDetails = $this->cnx->prepare($queryDetails);
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
        $cnx = Database::GetConnection();
        $query = "
            INSERT INTO client (loginClient, passwordClient, mailClient, idRoleClient) \n
            VALUES (:l, :p, :m, 2)";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":l", $login);
        $password = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(":p", $password);
        $stmt->bindParam(":m", $mail);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param string $login
     * @return bool True si existe, sinon False
     */
    public static function UserLoginExists(string $login):bool
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idClient \n
            FROM client \n
            WHERE loginClient = :l";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":l", $login);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @param string $mail
     * @return bool True si exists, sinon False
     */
    public static function UserMailExists(string $mail):bool
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idClient \n
            FROM client \n
            WHERE mailClient = :m";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":m", $mail);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function GetUserById(int $id):Client|bool
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idClient, loginClient, passwordClient, mailClient, idRoleClient \n
            FROM client \n
            WHERE idClient = :id";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Client");
        $stmt->execute();

        return $stmt->fetch();
    }
}