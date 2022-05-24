<?php

class ajaxController extends Controller
{
    public function getProduitByRef()
    {
        $ref = filter_input(INPUT_GET, "ref") ?? "";

        $produit = ProduitManager::GetProduit($ref);
        if($produit !== false) {
            $produit = $produit->ToArray();
        } else {
            $produit = [];
        }

        $this->renderAPI($produit);
    }
}