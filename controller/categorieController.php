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

        //Filtre
        $triSousCategs = $this->Request()->get("subcateg");
        $triDifficulte = $this->Request()->get("difficulty");
        $triOrder = $this->Request()->get("filters_tri_order");
        $triWay = $this->Request()->get("filters_tri_way");

        $produits = ProduitManager::GetProduitsByCateg($refCateg, $triSousCategs, $triDifficulte, $triOrder, $triWay);
        $sousCategs = CategorieManager::GetSousCategories($refCateg);
        $difficulties = DifficultiesManager::GetLesDifficultes();

        $this->render(
            "/categorie/afficCategorie.phtml", [
                "produits" => $produits,
                "sousCategs" => $sousCategs,
                "difficulties" => $difficulties,
                "triOrder" => $triOrder,
                "triWay" => $triWay,
                "triDifficulte" => $triDifficulte,
                "triSousCategs" => $triSousCategs
        ]);
    }
}