<?php
require_once  __DIR__.'/autoload.php';
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