<?php

class indexController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        $produits = $this->ProduitManager()->GetMostBoughtProduits(5);

        $this->render("/accueil/afficAccueil.phtml", ["produits" => $produits]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function search()
    {
        $r = $_GET["r"] ?? null;
        if($r == null) {
            $this->redirect("/");
        }

        $produits = $this->ProduitManager()->GetProduitsBySearch($r);

        $this->render("/accueil/afficAccueil.phtml", [
            "r" => $r,
            "produits" => $produits
        ]);
    }

    public static function error()
    {

    }
}