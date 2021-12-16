<?php
    /**
     * @var array $requires
     */
    require_once "ajaxUtils.php";
    require_once $requires["utils"];
    require_once $requires["database"];
    require_once $requires["client"];
    require_once $requires["client_manager"];

    $login    = Utils::nettoyerStr($_POST["login"]);
    $password = Utils::nettoyerStr($_POST["password"]);

    try {
        /**
         * @var Client|bool $client
         */
        if ($client = ClientManager::TryLeClient($login))
        {
            if (password_verify($password, $client->GetPassword()))
            {
                echo "true";
            }
        }
    }
    catch(Exception $ex) {
        echo "false";
    }

