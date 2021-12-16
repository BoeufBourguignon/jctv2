<?php

class panierController extends Controller
{
    public static function draw()
    {


        $view = ROOT."/views/panier/afficPanier.phtml";
        $params = array();

        self::render($view, $params);
    }
}