<?php
include "config.php";



$adm = new AdminService('dupa', 'haslo', $database);


$adm->registerUser("testowys3", "jasiek");

?>