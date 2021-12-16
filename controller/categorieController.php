<?php

class categorieController extends Controller
{
    public static function draw($params)
    {
        /**
         * @var $categorie
         */
        extract($params);
        try
        {
            if($idCateg = CategorieManager::GetIdByRef($categorie))
            {
                $produits = ProduitManager::GetProduitsByCateg($idCateg);
                $sousCategs = CategorieManager::GetSousCategories(CategorieManager::GetIdByRef($categorie));
                $difficulties = DifficultiesManager::GetLesDifficultes();
            }
            else
            {
                throw new Exception("La catégorie n'existe pas");
            }
        }
        catch (Exception $e)
        {
            $modal = new ClassModalManager();
            $modal->getModalError($e->getMessage())->buildModal();
            die();
        }

        $view = ROOT."/views/categorie/afficCategorie.phtml";
        $params = array();
        $params["produits"] = $produits;
        $params["sousCategs"] = $sousCategs;
        $params["difficulties"] = $difficulties;
        self::render($view, $params);
    }
}