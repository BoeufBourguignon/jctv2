<?php
const ROOT = "../../";
require_once(ROOT."model/Utils.php");
require_once(ROOT."model/class/_Database.php");
require_once(ROOT."model/class/Difficulty.php");
require_once(ROOT."model/class/Produit.php");
require_once(ROOT."model/manager/DifficultiesManager.php");
require_once(ROOT."model/manager/ProduitManager.php");
require_once(ROOT."model/manager/CategorieManager.php");

$categ          = CategorieManager::GetIdByRef(Utils::nettoyerStr($_POST["categ"]));
$subcategs      = count(json_decode(Utils::nettoyerStr($_POST["subcategs"]))) ? json_decode(Utils::nettoyerStr($_POST["subcategs"])) : null;
$difficulties   = count(json_decode(Utils::nettoyerStr($_POST["difficulties"]))) ? json_decode(Utils::nettoyerStr($_POST["difficulties"])) : null;
$order          = Utils::nettoyerStr($_POST["order"]);
$way            = Utils::nettoyerStr($_POST["way"]);

if (in_array(strtolower($order), ["prix", "difficulte"]) && in_array(strtolower($way), ["asc", "desc"])) {
    try {
        $produits = ProduitManager::GetProduitsByCateg($categ, $order, $way, $subcategs, $difficulties);
    } catch (Exception $e) {
        Utils::callModalAlert(["content" => $e->getMessage()]);
        die();
    }
    foreach ($produits as $produit) {
        include($_SERVER["DOCUMENT_ROOT"] . "/views/produit/ficheProduit.inc");
    }
}
else {
    Utils::callModalAlert([
        "title"=>"Alerte fraude",
        "content"=>"Vous avez essayé de tromper le bourmestre de ce village. Honte sur vous petit fdp."
    ]);
}
