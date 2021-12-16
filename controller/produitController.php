<?php

class produitController extends Controller
{
    public static function draw($params)
    {
        /**
         * @var $produit
         */
        extract($params);
        try
        {
            $produit = ProduitManager::GetProduitByRef($produit);
            $selection = array();
            for($i = 0; $i < 3; $i++)
            {
                $selection[] = ProduitManager::GetRandomProduit();
            }
        }
        catch (Exception $e)
        {
            $modal = new ClassModalManager();
            $modal->getModalError($e->getMessage())->buildModal();
            die();
        }

        $view = ROOT."/views/produit/afficProduit.phtml";
        $params = array();
        $params["produit"] = $produit;
        $params["selection"] = $selection;
        self::render($view, $params);
    }
}