<?php
include "OperationManager.php";
include "config.php";

while(true) {


    $stdin = fopen('php://stdin', 'r');

    $fg =  fgets($stdin, 1024);

    $jsonIterator = new RecursiveIteratorIterator(
        new RecursiveArrayIterator(json_decode($fg, TRUE)),
        RecursiveIteratorIterator::SELF_FIRST);

    $operator = new OperationManager();

    foreach ($jsonIterator as $key => $val) {
        if(is_array($val)) {
            echo "$key\n";
            $operator->functionName = $key;

        } else {
            $operator->args[$key]= $val;
        }
    }

    $operator->execute();
    echo "\n";

}

?>