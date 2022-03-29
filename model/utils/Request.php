<?php
require_once(MANAGERS . "/ClientManager.php");


class Request
{
    private array $get = array();
    private array $post = array();
    private ?Client $user;

    public function __construct()
    {
        foreach($_GET as $key => $value) {
            if(($key != 'controller') && ($key != 'action')) {
                $this->get[$key] = $value;
            }
        }
        $this->post = $_POST;
        $this->user = isset($_SESSION['logged-in-user-id']) ? ClientManager::GetUserById($_SESSION['logged-in-user-id']) : null;
        if($this->user == false) {
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