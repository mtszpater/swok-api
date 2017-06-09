<?php
include_once "App.php";

$app = new App();

while(true) {
    try {
        $app->readLine();
        $app->execute();
    }
    catch(Exception $exception)
    {
        exit;
    }
}
?>