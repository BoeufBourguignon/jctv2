<?php

class indexController extends Controller
{
    public static function draw()
    {
        $produit = ProduitManager::GetRandomProduit();

        $view = ROOT."/views/accueil/afficAccueil.phtml";
        $params = array();
        $params["produit"] = $produit;
        self::render($view, $params);
    }
}