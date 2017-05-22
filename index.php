<?php
include "config.php";



if ($database->has_perm("dupa", "haslo") == true) echo "ok"; else echo "dupa";


?>