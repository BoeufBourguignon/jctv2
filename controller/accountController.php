<?php
// controller/accountController.php

/**
 * Controller connexion ou inscription ou compte
 * @author Thibaud Leclere
 * @date 22/10/2021
 */
class accountController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        //Si pas connecté, on redirige vers la page de connexion
        if ($this->Request()->user() == null) {
            $this->redirect("/account");
        }
        $this->render(
            "/account/afficAccount.phtml", [
                "client" => $this->Request()->user()
            ]
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function historique()
    {
        if ($this->Request()->user() == null) {
            $this->redirect("/account");
        }
        $this->render("/account/afficHistorique.phtml", [
            "historique" => $this->ClientManager()->GetHistorique($this->Request()->user())
        ]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function login()
    {
        if($this->Request()->user() != null) {
            $this->redirect("/account");
        }

        if($this->Request()->post("form-login-submitted") !== false) {
            $jwt = $this->Request()->post("jwt");
            if($jwt && Encoder::verifyJWT($jwt)) {
                try {
                    $inUsername = $this->Request()->post("login") != false ? Utils::nettoyerStr($this->Request()->post("login")) : "";
                    $inPassword = $this->Request()->post("password") != false ? Utils::nettoyerStr($this->Request()->post("password")) : "";
                    $_SESSION["lastUsername"] = $inUsername;

                    if(strlen($inUsername) && strlen($inPassword)) {
                        $client = $this->ClientManager()->TryLeClient($inUsername);
                        if(    $client !== false
                            && password_verify($inPassword, $client->GetPassword())
                        ) {
                            $this->ClientManager()->DoConnect($client);
                        } else {
                            $_SESSION["login-error"] = "Le nom d'utilisateur n'existe pas ou le mot de passe est incorrect";
                        }
                    } else {
                        $_SESSION["login-error"] = "Les champs ne doivent pas être vides";
                    }
                } catch(Exception $ex) {
                    $_SESSION["login-error"] = "Une erreur est survenue, veuillez contacter le webmaster";
                }
            } else {
                $_SESSION["login-error"] = "Une erreur est survenue, veuillez réessayer";
            }
            $this->redirect("/account");
        } else {
            $this->render(
                "/account/afficConnect.phtml", [
                    "jwt" => Encoder::createJWT()
                ]
            );
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function create()
    {
        if($this->Request()->user() != null) {
            $this->redirect("/account");
        }

        $this->render(
            "/account/afficCreateAccount.phtml", [
                "jwt" => Encoder::createJWT()
            ]
        );
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

    /**
     * @return void
     */
    public function doLogout()
    {
        if($this->Request()->post("form-disconnect") !== false)
        {
            $this->ClientManager()->DoLogout();
        }
        $this->redirect("/account");
    }
}