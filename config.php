<?php
/*
* Raportujmy wszystkie błędy ( ͡° ͜ʖ ͡°)
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "class.database.manager.php";
include "class.member.php";
$database = new DatabaseManager();

?>