<?php

class headerController extends Controller
{
    public static function draw()
    {
        try {
            $categs = CategorieManager::GetLesCategories();
            $accountMsg = isset($_SESSION['logged-in-user-id'])
                ? ClientManager::GetUserById($_SESSION['logged-in-user-id'])->GetLogin()
                : "Connectez-vous";
        } catch (Exception $e) {
            $modal = new ClassModalManager();
            $modal->getModalErrorReloadOnClose($e->getMessage())->buildModal();
            die();
        }

        $view = ROOT."/views/header/afficHeader.phtml";
        $params = array();
        $params     ["categs"] = $categs     ;
        $params ["accountMsg"] = $accountMsg ;
        self::render($view, $params);
    }
}