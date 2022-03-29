<?php
require_once(_CLASS . "/_Database.php");

require_once(_CLASS . "/Categorie.php");
require_once(_CLASS . "/Difficulty.php");
require_once(_CLASS . "/Produit.php");
require_once(_CLASS . "/Client.php");

require_once(MODEL . "/Utils.php");
require_once(MANAGERS . "/BaseManager.php");
require_once(MANAGERS . "/CategorieManager.php");
require_once(MANAGERS . "/DifficultiesManager.php");
require_once(MANAGERS . "/ProduitManager.php");
require_once(MANAGERS . "/ClientManager.php");
require_once(MANAGERS . "/PanierManager.php");

require_once(CONTROL . "/_Controller.php");
require_once(UTILS . "/Request.php");
require_once(UTILS . "/Encoder.php");