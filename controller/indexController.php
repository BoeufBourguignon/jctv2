<?php

class indexController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        $this->render("/accueil/afficAccueil.phtml");
    }

    /**
     * @return void
     * @throws Exception
     */
    public function search()
    {
        $r = $_GET["r"] ?? null;

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