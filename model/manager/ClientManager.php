<?php
require_once(_CLASS . "/Client.php");


class ClientManager
{
    public static function TryLeClient(string $login)
    {
        $cnx = Database::GetConnection();
        $query = "
            SELECT idClient, loginClient, passwordClient, mailClient, idRoleClient \n
            FROM client \n
            WHERE loginClient = :l ";
        $stmt = $cnx->prepare($query);
        $stmt->bindParam(":l", $login);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Client");
        $stmt->execute();

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