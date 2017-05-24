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
                echo "$key\n";
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


<!--$connection = pg_connect("host=localhost port=5432 dbname=springbootdb user=ja password=");-->
<!---->
<!---->
<!---->
<!--$query = pg_query($connection, "-->
<!--SELECT registrations_on_events.login, id as talk, date_start as start_timestamp, title, room FROM registrations_on_events-->
<!--JOIN talk ON registrations_on_events.event_name=talk.event_name-->
<!--WHERE registrations_on_events.login = 'admin'-->
<!--ORDER BY talk.date_start ASC;");-->
<!---->
<!---->
<!--echo json_encode(pg_fetch_all($query));-->
<!---->
<!--var_dump(pg_fetch_all($query));-->
