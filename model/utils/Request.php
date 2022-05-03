<?php
require_once(MANAGERS . "/ClientManager.php");


class Request
{
    private array $get = array();
    private array $post = array();
    private ?Client $user;

    public function __construct()
    {
        //GET
        foreach($_GET as $key => $value) {
            if(($key != 'controller') && ($key != 'action')) {
                if(!is_array($value)) {
                    $this->get[$key] = Utils::nettoyerStr($value);
                } else {
                    $this->get[$key] = $value;
                }
            }
        }
        //POST
        foreach($_POST as $key => $value) {
            if(!is_array($value)) {
                $this->post[$key] = Utils::nettoyerStr($value);
            } else {
                $this->post[$key] = $value;
            }
        }

        //USER
        //Regarde si l'utilisateur peut être connecté (dans la base de données du site)
        $cid = $_COOKIE["cid"] ?? null;
        $cuid = $_COOKIE["cuid"] ?? null;
        if($cid != null && $cuid != null && ClientManager::CanConnect($cid, $cuid)) {
            $this->user = ClientManager::GetUserById($cuid);
        } else {
            $this->user = null;
        }
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function get(string $key = null): mixed
    {
        if($key == null) {
            return $this->get;
        } else {
            return $this->get[$key] ?? false;
        }
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function post(string $key = null): mixed
    {
        if($key == null) {
            return $this->post;
        } else {
            return $this->post[$key] ?? false;
        }
    }

    /**
     * @return Client|null
     */
    public function user(): Client|null
    {
        return $this->user;
    }
}