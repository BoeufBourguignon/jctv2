<?php

class indexController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        $produit = $this->ProduitManager()->GetRandomProduit();

        $this->render(
            "/accueil/afficAccueil.phtml", [
                "produit" => $produit
        ]);
    }

    public static function error()
    {

    }
}