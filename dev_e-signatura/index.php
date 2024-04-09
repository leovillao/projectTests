<?php
//unset($_SESSION);
session_start();

include "core/autoload.php";
$debug = true;
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
$lb = new Lb();
$lb->loadModule("index");
Core::$debug_sql = true;
?>