<?php

class categorieController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function draw()
    {
        $refCateg = $this->Request()->get("categorie");

        $produits = ProduitManager::GetProduitsByCateg($refCateg);
        $sousCategs = CategorieManager::GetSousCategories($refCateg);
        $difficulties = DifficultiesManager::GetLesDifficultes();

        $this->render(
            "/categorie/afficCategorie.phtml", [
                "produits" => $produits,
                "sousCategs" => $sousCategs,
                "difficulties" => $difficulties
        ]);
    }
}