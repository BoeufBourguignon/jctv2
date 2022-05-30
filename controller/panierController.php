<?php

class panierController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        $this->render("/panier/afficPanier.phtml");
    }

    public function add()
    {
        $produit = $this->Request()->post("refProduit");
        $qte = $this->Request()->post("qte");

        if($produit != false && $qte != false) {
            $this->Panier()->Add($produit, $qte);
        }

        $this->redirect("/produit/" . $produit);
    }

    public function update()
    {
        $this->Panier()->Update($this->Request()->post("panier"));

        $this->redirect("/panier");
    }
}