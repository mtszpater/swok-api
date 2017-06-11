<?php
require_once 'autoload.php';

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