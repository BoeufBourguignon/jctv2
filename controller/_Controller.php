<?php

use JetBrains\PhpStorm\NoReturn;

abstract class Controller
{
    private Request $request;

    private CategorieManager $CategorieManager;
    private ClientManager $ClientManager;
    private DifficultiesManager $DifficultiesManager;
    private ProduitManager $ProduitManager;

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

    protected function CategorieManager(): CategorieManager
    {
        if(!isset($this->CategorieManager)) {
            $this->CategorieManager = new CategorieManager();
        }
        return $this->CategorieManager;
    }

    protected function ClientManager(): ClientManager
    {
        if(!isset($this->ClientManager)) {
            $this->ClientManager = new ClientManager();
        }
        return $this->ClientManager;
    }

    protected function DifficultiesManager(): DifficultiesManager
    {
        if(!isset($this->DifficultiesManager)) {
            $this->DifficultiesManager = new DifficultiesManager();
        }
        return $this->DifficultiesManager;
    }

    protected function ProduitManager(): ProduitManager
    {
        if(!isset($this->ProduitManager)) {
            $this->ProduitManager = new ProduitManager();
        }
        return $this->ProduitManager;
    }

    /**
     * @param string $view
     * @param array $params
     * @return void
     * @throws Exception
     */
    protected function render(string $view, array $params = array())
    {
        extract($params);
        require_once(VUES . "/base.php");
    }

    /**
     * @param string $url
     * @return void
     */
    #[NoReturn] protected function redirect(string $url)
    {
        header("Location: ".$url);
        exit;
    }
}