<?php

error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', '1');

require_once 'Controller.php';

$controller = new Controller();

if(!isset($_GET['do'])) {
    header("Location: ../index.php");
}

$do = $_GET['do'];

if($do == 'save' && isset($_GET['file']) && isset($_GET['link'])) {
    $controller->save($_GET['file'],$_GET['link']);
}

if ($do == 'update') {
    $controller->update();
}
else {
    header("Location: ../index.php");
}