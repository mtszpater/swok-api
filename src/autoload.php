<?php
function autoLoadServices($className) {
    $filename = __DIR__ . "/service/" . $className . ".php";
    if (is_readable($filename)) {
        require_once $filename;
    }
}

function autoloadController($className) {
    $filename =  __DIR__ . "/" . $className . ".php";
    if (is_readable($filename)) {
        require_once $filename;
    }
}

spl_autoload_register("autoLoadServices");
spl_autoload_register("autoloadController");