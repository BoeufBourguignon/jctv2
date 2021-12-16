<?php
require_once(ROOT . "/model/class/_Database.php");

require_once(ROOT . "/model/class/Categorie.php");
require_once(ROOT . "/model/class/Difficulty.php");
require_once(ROOT . "/model/class/Produit.php");
require_once(ROOT . "/model/class/Client.php");

require_once(ROOT."/model/Utils.php");
// Déjà fait dans l'index -- require_once(ROOT."/model/utils/autoload.php");
require_once(ROOT."/model/manager/CategorieManager.php");
require_once(ROOT."/model/manager/DifficultiesManager.php");
require_once(ROOT."/model/manager/ProduitManager.php");
require_once(ROOT."/model/manager/ClientManager.php");

require_once(ROOT."/controller/_Controller.php");