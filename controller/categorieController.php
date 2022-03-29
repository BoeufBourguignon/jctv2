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
        if($refCateg != false) {
            $produits = $this->ProduitManager()->GetProduitsByCateg($refCateg);
            $sousCategs = $this->CategorieManager()->GetSousCategories($refCateg);
            $difficulties = $this->DifficultiesManager()->GetLesDifficultes();
        } else {
            throw new Exception("Aucune catégorie spécifiée");
        }

        $this->render(
            "/categorie/afficCategorie.phtml", [
                "produits" => $produits,
                "sousCategs" => $sousCategs,
                "difficulties" => $difficulties
        ]);
    }
}