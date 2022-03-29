<?php
session_start();

const ROOT = __DIR__;
const MODEL = ROOT . "/model";
const UTILS = MODEL . "/utils";
const MANAGERS = MODEL . "/manager";
const CONTROL = ROOT . "/controller";
const _CLASS = MODEL . "/class";
const VUES = ROOT . "/views";

const DEFAULT_CONTROLLER = "index";
const DEFAULT_ACTION = "draw";

//Load utils
//require_once(ROOT.'/model/utils/autoload.php');
//$modal = new ClassModalManager();

require_once(ROOT . "/autoload.php");

$controller = $_GET["controller"] ?? DEFAULT_CONTROLLER;
$action = $_GET["action"] ?? DEFAULT_ACTION;

$controller .= "Controller";
$fileController = ROOT."/controller/".$controller.".php";

try {
    if(file_exists($fileController)) {
        require_once $fileController;
        $controllerObj = new $controller();
        if(method_exists($controller, $action)) {
            $controllerObj->$action();
        } else {
            throw new Exception("L'action " . $action . " n'existe pas dans le controleur " . $controller);
        }
    } else {
        throw new Exception("Le controleur " . $controller . " n'existe pas");
    }
} catch(Exception $e) {
    var_dump($e);
}
