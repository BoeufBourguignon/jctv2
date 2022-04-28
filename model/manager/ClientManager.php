<?php
require_once(_CLASS . "/Client.php");


class ClientManager
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

        //Ajout la connexion en BDD
        $cnx = Database::GetConnection();
        $query = "
            INSERT INTO user_connection (idClient, idConnection) VALUES
            (:cuid, :cid)
        ";
        $stmt = $cnx->prepare($query);
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
            $cnx = Database::GetConnection();
            $query = "
                DELETE FROM user_connection
                WHERE idClient = :cuid
                AND idConnection = :cid
            ";
            $stmt = $cnx->prepare($query);
            $stmt->execute([":cid" => $cid, ":cuid" => $cuid]);
        }

        setcookie("cid", "", time() - 1800, "/");
        setcookie("cuid", "", time() - 1800, "/");
    }

    public function TryLeClient(string $login): Client|false
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idClient, loginClient, passwordClient, mailClient, idRoleClient
            FROM client
            WHERE loginClient = :l
        ";
        $stmt = $cnx->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Client");
        $stmt->execute([":l" => $login]);

        return $stmt->fetch();
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