<?php
session_start();
const ROOT = __DIR__;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jean CasseTête - Vente de casse-têtes en ligne</title>
    <link rel="icon" href="<?=ROOT?>/assets/img/logov2.png">
    <!--STYLES-->
    <link rel="stylesheet" href="<?=ROOT?>/assets/custom/styles/main.css">
    <!--OTHER JS-->
    <script src="<?=ROOT?>/assets/others/popperJS-2/popperJS.js"></script>
    <script src="<?=ROOT?>/assets/others/jquery-3.6.0-dist/jquery-3.6.0.js"></script>
    <script src="<?=ROOT?>/assets/others/bootstrap-5.1.0-dist/js/bootstrap.js"></script>
    <script src="https://kit.fontawesome.com/22ce38b26b.js"></script>
</head>
<body>
<?php
const DEFAULT_CONTROLLER = "index";
const DEFAULT_ACTION = "draw";
//Load utils
require_once(ROOT.'/model/utils/autoload.php');
$modal = new ClassModalManager();
require_once(ROOT."/autoload.php");
//HEADER
require_once(ROOT."/controller/headerController.php");
headerController::draw();
echo "<div id=\"main_wrapper\">";

if (    isset($_GET["controller"]) && !empty($_GET["controller"])
     && isset($_GET["action"])     && !empty($_GET["action"]) )
{
    $controller = $_GET["controller"];
    $action = $_GET["action"];
}
else if (    isset($_GET["controller"]) && !empty($_GET["controller"]) )
{
    $controller = $_GET["controller"];
    $action = DEFAULT_ACTION;
}
else
{
    $controller = DEFAULT_CONTROLLER;
    $action = DEFAULT_ACTION;
}
$controller .= "Controller";
$fileController = ROOT."/controller/".$controller.".php";
$params = array();
foreach($_GET as $key => $value){
    if(($key != 'controller') && ($key != 'action')){
        $params[$key] = $value;
    }
}

if (file_exists($fileController))
{
    require_once $fileController;

    if (method_exists($controller, $action))
    {
        $controller::$action($params);
    }
    else
    {
        Utils::callModalAlert(["content"=>"L'action ".$action." n'existe pas dans le controller ".$controller]);
    }
}
else
{
    Utils::callModalAlert(["content"=>"Le controller ".$controller." n'existe pas"]);
}

echo "</div>";
?>


<!--MY JS-->
<script src="assets/custom/js/tri.js"></script>
<script src="assets/custom/js/mainJS.js"></script>
</body>
</html>