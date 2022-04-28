<?php

class PanierManager extends BaseManager
{
    private ?Client $client;

    private array $panier = array();

    public function __construct(?Client $client)
    {
        parent::__construct();

        $this->client = $client;

        if($this->client == null) {
            $this->panier = isset($_COOKIE["panier"]) ? json_decode($_COOKIE["panier"], true) : [];
        }
    }

    public function Panier()
    {
        $produits = array();
        foreach($this->panier as $refProduit => $qte) {
            $produits[$refProduit] = [
                "produit" => $this->ProduitManager()->GetProduitByRef($refProduit),
                "qte" => $qte
            ];
        }
        return $produits;
    }

    public function QteTotale()
    {
        $qteTotale = 0;
        foreach($this->panier as $qte) {
            $qteTotale += $qte;
        }
        return $qteTotale;
    }

    public function Add(string $refProduit, int $qte)
    {
        if($qte != 0) {
            if(isset($this->panier[$refProduit]) && is_int($this->panier[$refProduit])) {
                $this->panier[$refProduit] += $qte;
            } else {
                $this->panier[$refProduit] = $qte;
            }
            $this->UpdateCookieOrBDD();
        }
    }

    public function Update(array $panier)
    {
        $newPanier = array();
        foreach($panier as $refProduit => $qte) {
            if($qte > 0) {
                $newPanier[$refProduit] = $qte;
            }
        }
        $this->panier = $newPanier;
        $this->UpdateCookieOrBDD();
    }

    private function UpdateCookieOrBDD()
    {
        if($this->client == null) {
            setcookie("panier", json_encode($this->panier), time() + 365*24*60*60, "/");
        }
    }
}