<?php
/*
* Raportujmy wszystkie błędy ( ͡° ͜ʖ ͡°)
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "DatabaseManager.php";
include "AdminService.php";

$database = new DatabaseManager();


$database->registerUser("testowy", "janek");


?>