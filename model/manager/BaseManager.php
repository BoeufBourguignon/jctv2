<?php

abstract class BaseManager
{
    protected PDO $cnx;

    private CategorieManager $CategorieManager;
    private ClientManager $ClientManager;
    private DifficultiesManager $DifficultiesManager;
    private ProduitManager $ProduitManager;

    public function __construct()
    {
        $this->cnx = Database::GetConnection();
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
}