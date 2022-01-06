<?php
// controller/accountController.php

/**
 * Controller connexion ou inscription ou compte
 * @author Thibaud Leclere
 * @date 22/10/2021
 */
class accountController extends Controller
{
    //Si connecté on redirige vers page du compte
    private static function isLoggedIn()
    {
        if (isset($_SESSION['logged-in-user-id']))
            Utils::redirect("/account");
    }

    public static function draw()
    {
        if (!isset($_SESSION['logged-in-user-id']))
            Utils::redirect("/account/login");

        $params = array();

        $params["client"] = ClientManager::GetUserById($_SESSION['logged-in-user-id']);
        $view = ROOT."/views/account/afficAccount.phtml";

        self::render($view, $params);
    }

    public static function login()
    {
        self::isLoggedIn();

        $params = array();

        $params["jwt"] = Encoder::createJWT();
        $view = ROOT."/views/account/afficConnect.phtml";

        self::render($view, $params);
    }

    public static function doLogin()
    {
        if (isset($_POST["form-login-submitted"]))
            //On vient du formulaire de connection
        {
            if (isset($_POST["jwt"]) && Encoder::verifyJWT($_POST["jwt"]))
                //Je vérifie le token
            {
                try {
                    $inUsername = isset($_POST["login"]) ? Utils::nettoyerStr($_POST["login"]) : "";
                    $inPassword = isset($_POST["password"]) ? Utils::nettoyerStr($_POST["password"]) : "";

                    if (strlen($inUsername) && strlen($inPassword))
                    {
                        if ($client = ClientManager::TryLeClient($inUsername))
                        {
                            if (password_verify($inPassword, $client->GetPassword()))
                            {
                                //Si les infos sont bonnes / existent
                                //On met l'id de la personne connectée en session
                                $_SESSION["logged-in-user-id"] = $client->GetId();
                            }
                            else
                            {
                                $_SESSION["error-login"] = "Le mot de passe est incorrect";
                            }
                        }
                        else
                        {
                            $_SESSION["error-login"] = "Le nom d'utilisateur n'existe pas";
                        }
                    }
                    else
                    {
                        $_SESSION["error-login"] = "Les champs ne doivent pas être vides";
                    }
                }
                catch (Exception $ex)
                {
                    $_SESSION["error-login"] = "Une erreur est intervenue, veuillez contacter le webmaster";
                }
            }
            else
                //Erreur à cause du token
            {
                $_SESSION["error-login-token"] = true;
            }
        }
        Utils::redirect("/account");
    }

    public static function create()
    {
        self::isLoggedIn();

        $params["jwt"] = Encoder::createJWT();
        $view = ROOT."/views/account/afficCreateAccount.phtml";

        self::render($view, $params);
    }

    public static function doCreate()
    {
        if (isset($_POST["form-create-account-submitted"]))
            //On vient du formulaire de création de compte
        {
            if (isset($_POST["jwt"]) && Encoder::verifyJWT($_POST["jwt"]))
            {
                $regexPwd = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
                $regexMail = "/^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$/";

                $login          = isset($_POST["cLogin"])          ? Utils::nettoyerStr($_POST["cLogin"])          : "";
                $password       = isset($_POST["cPassword"])       ? Utils::nettoyerStr($_POST["cPassword"])       : "";
                $passwordVerify = isset($_POST["cPasswordVerify"]) ? Utils::nettoyerStr($_POST["cPasswordVerify"]) : "";
                $mail           = isset($_POST["cMail"])           ? Utils::nettoyerStr($_POST["cMail"])           : "";

                $noError = true;

                if (strlen($login) && strlen($password) && strlen($passwordVerify) && strlen($mail))
                    //Tous les champs ont été remplis
                {
                    try
                    {
                        $loginExists = ClientManager::UserLoginExists($login);
                        $mailExists = ClientManager::UserMailExists($mail);

                        if (!$loginExists && !$mailExists)
                            //Si le mail et le login n'existent pas
                        {
                            if (preg_match($regexPwd, $password))
                                //Mot de passe au bon format
                            {
                                if ($password !== $passwordVerify)
                                    //Le mot de passe n'est pas vérifié
                                {
                                    $_SESSION["error-create"][] = "Les mots de passe ne correspondent pas";
                                    $noError = false;
                                }
                                else if (strlen($password) >= 40) {
                                    $_SESSION["error-create"][] = "Le mot de passe doit faire moins de 40 caractères";
                                    $noError = false;
                                }
                                else if (strlen($password) < 6) {
                                    $_SESSION["error-create"][] = "Le mot de passe doit faire 6 caractères ou plus";
                                    $noError = false;
                                }
                                else if (strlen($login) > 20) {
                                    $_SESSION["error-create"][] = "L'identifiant doit faire moins de 20 caractères";
                                    $noError = false;
                                }
                            }
                            else
                                //Mot de passe pas au bon format
                            {
                                $_SESSION["error-create"][] = "Le mot de passe doit contenir une majuscule, une minuscule, un chiffre, un caractère spécial (@,$,!,%,*,?,&) et faire plus de 8 caractères";
                                $noError = false;
                            }

                            if (!preg_match($regexMail, $mail))
                                //Mail pas au bon format
                            {
                                $_SESSION["error-create"][] = "L'adresse mail n'est pas conforme";
                                $noError = false;
                            }
                        }
                        else
                        {
                            if($loginExists)
                                $_SESSION["error-create"][] = "Le nom d'utilisateur existe déjà";
                            if($mailExists)
                                $_SESSION["error-create"][] = "L'adresse mail existe déjà";
                            $noError = false;
                        }
                    }
                    catch(Exception $e)
                        //Problème avec la bdd
                    {
                        $_SESSION["error-create"] = "Une erreur est intervenue, veuillez contacter le webmaster";
                        $noError = false;
                    }
                }
                else
                    //Un champ est vide
                {
                    $_SESSION["error-create"][] = "Aucun champ ne doit être vide";
                    $noError = false;
                }

                if($noError)
                    //Si le compte peut être créé
                {
                    try
                    {
                        ClientManager::AddLeClient($login, $password, $mail);

                        $_SESSION["success-create"] = true;
                        Utils::redirect("/account/login");
                    }
                    catch(Exception $e)
                    {
                        $_SESSION["error-create"] = "Une erreur est intervenue, veuillez contacter le webmaster";
                    }
                }
            }
            else
            {
                $_SESSION["error-create-token"] = true;
            }
            Utils::redirect("/account/create");
        }
        //Si on ne vient pas du formulaire
        Utils::redirect("/account");
    }

    //On logout, when clicking disconnect button
    //Redirect to
    //  /account
    public static function doLogout()
    {
        if (isset($_POST["form-disconnect"]))
        {
            session_destroy();
        }

        Utils::redirect("/account");
    }
}