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

    public function getSousCategoriesByCategorie()
    {
        $refCateg = filter_input(INPUT_GET, "refCateg") ?? "";

        $sousCategs_obj = CategorieManager::GetSousCategories($refCateg);
        $sousCategs = array();
        foreach($sousCategs_obj as $sousCateg) {
            $sousCategs[] = $sousCateg->ToArray();
        }

        $this->renderAPI($sousCategs);
    }

    public function getCategorieByRef()
    {
        $ref = $this->Request()->get("ref");

        $categorie = CategorieManager::GetCategorie($ref);
        if($categorie !== false) {
            $categorie = $categorie->ToArray();
        } else {
            $categorie = [];
        }

        $this->renderAPI($categorie);
    }

    public function test()
    {
        var_dump($_GET);
        var_dump($_POST);
    }
}