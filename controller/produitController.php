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
        $produit = null;
        $selection = array();
        if($refProduit != false) {
            $produit = $this->ProduitManager()->GetProduitByRef($refProduit);
            for($i = 0; $i < 3; $i++)
            {
                $selection[] = $this->ProduitManager()->GetRandomProduit();
            }
        } else {
            throw new Exception("Aucun produit spécifiée");
        }

        self::render(
            "produit/afficProduit.phtml", [
                "produit" => $produit,
                "selection" => $selection
        ]);
    }
}