<?php

class produitController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        $refProduit = $this->Request()->get("produit");
        $produit = ProduitManager::GetProduit($refProduit);

        self::render(
            "produit/afficProduit.phtml", [
                "produit" => $produit
        ]);
    }
}