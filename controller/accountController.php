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
            $this->redirect("/account/login");
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
            "historique" => ClientManager::GetHistorique($this->Request()->user())
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
                    $inUsername = $this->Request()->post("login");
                    $inPassword = $this->Request()->post("password");
                    $_SESSION["lastUsername"] = $inUsername;

                    if(    $inUsername !== false && strlen($inUsername) > 0
                        && $inPassword !== false && strlen($inPassword) > 0
                    ) {
                        $client = ClientManager::TryLeClient($inUsername);
                        if(    $client !== false
                            && password_verify($inPassword, $client->GetPassword())
                        ) {
                            ClientManager::DoConnect($client);
                            unset($_SESSION["lastUsername"]);
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

        $regexPwd = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/";
        $regexMail = "/^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$/";

        if($this->Request()->post("form-create-account-submitted") !== false) {
            var_dump("oui");
            $jwt = $this->Request()->post("jwt");
            if($jwt && Encoder::verifyJWT($jwt)) {
                $login = $this->Request()->post("cLogin");
                $password = $this->Request()->post("cPassword");
                $passwordVerify = $this->Request()->post("cPasswordVerify");
                $mail = $this->Request()->post("cMail");
                $_SESSION["triedUsername"] = $login;
                $_SESSION["triedMail"] = $mail;

                if(    $login !== false && strlen($login) > 0
                    && $password !== false && strlen($password) > 0
                    && $passwordVerify !== false && strlen($passwordVerify) > 0
                    && $mail !== false && strlen($mail) > 0
                ) {
                    try {
                        $loginExists = ClientManager::UserLoginExists($login);
                        $mailExists = ClientManager::UserMailExists($mail);

                        if($loginExists) {
                            $_SESSION["create-error"][] = "Le nom d'utilisateur existe déjà";
                        } else if ($mailExists) {
                            $_SESSION["create-error"][] = "Cette adresse email est déjà prise";
                        } else {
                            $passwordVerified = false;
                            $mailVerified = false;

                            if(preg_match($regexPwd, $password)) {
                                if($passwordVerify !== $password) {
                                    $_SESSION["create-error"][] = "Les mots de passes ne correspondent pas";
                                } else if(strlen($password) >= 40) {
                                    $_SESSION["create-error"][] = "Le mot de passe doit faire moins de 40 caractères";
                                } else if(strlen($password) < 6) {
                                    $_SESSION["create-error"][] = "Le mot de passe doit faire 6 caractères ou plus";
                                } else if (strlen($login) > 20) {
                                    $_SESSION["create-error"][] = "L'identifiant doit faire moins de 20 caractères";
                                } else {
                                    $passwordVerified = true;
                                }
                            } else {
                                $_SESSION["create-error"][] = "Le mot de passe doit contenir une majuscule, 
                                une minuscule, un chiffre, un caractère spécial (@,$,!,%,*,?,&) et faire plus 
                                de 8 caractères";
                            }
                            if(preg_match($regexMail, $mail)) {
                                $mailVerified = true;
                            } else {
                                $_SESSION["create-error"][] = "L'adresse mail n'est pas conforme";
                            }

                            if($passwordVerified && $mailVerified) {
                                ClientManager::AddLeClient($login, $password, $mail);
                                unset($_SESSION["triedMail"]);
                                unset($_SESSION["triedUsername"]);
                                $_SESSION["create-success"] = true;
                                $this->redirect("/account");
                            }
                        }
                    } catch(Exception $ex) {
                        $_SESSION["create-error"] = ["Une erreur est survenue, veuillez contacter le webmaster"];
                    }
                } else {
                    $_SESSION["create-error"] = ["Tous les champs doivent être remplis"];
                }
            }
            $this->redirect("/account/create");
        } else {
            $this->render(
                "/account/afficCreateAccount.phtml", [
                    "jwt" => Encoder::createJWT()
                ]
            );
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        ClientManager::DoLogout();

        $this->redirect("/account");
    }
}