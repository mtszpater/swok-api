<?php
function autoLoadServices($className) {
    $filename = "service/" . $className . ".php";
    if (is_readable($filename)) {
        require_once $filename;
    }
}

function autoloadController($className) {
    $filename = "" . $className . ".php";
    if (is_readable($filename)) {
        require_once $filename;
    }
}

spl_autoload_register("autoLoadServices");
spl_autoload_register("autoloadController");