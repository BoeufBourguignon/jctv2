<?php

abstract class Controller
{
    private Request $request;
    private PanierManager $Panier;

    public function __construct()
    {
        $this->request = new Request();
        $this->Panier = new PanierManager($this->request->user());
    }

    protected function Panier(): PanierManager
    {
        return $this->Panier;
    }

    protected function Request(): Request
    {
        return $this->request;
    }

    /**
     * @param string $view
     * @param array $params
     * @param bool $hasNav
     * @return void
     */
    protected function render(string $view, array $params = array(), bool $hasNav = true)
    {
        extract($params);
        require_once(VUES . "/base.php");
    }

    /**
     * @param mixed $value
     */
    protected function renderAPI(mixed $value)
    {
        $json = json_encode($value);
        echo $json !== false ? $json : "[]";
    }

    /**
     * @param string $url
     * @return void
     */
    protected function redirect(string $url)
    {
        header("Location: ".$url);
        exit;
    }
}