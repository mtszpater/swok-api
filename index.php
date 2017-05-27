<?php
include "OperationManager.php";
include "config.php";


while(true) {

    try {
        $stdin = fopen('php://stdin', 'r');

        $fg = fgets($stdin, 1024);

        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($fg, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);

        $operator = new OperationManager();

        foreach ($jsonIterator as $key => $val) {
            if (is_array($val)) {
                $operator->functionName = $key;

            } else {
                $operator->args[$key] = $val;
            }
        }

        $operator->execute();
        echo "\n";
    }
    catch(Exception $exception)
    {
        echo "program exit\n";
        exit;
    }

}

?>