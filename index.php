<?php
include_once "OperationManager.php";
include_once "ConsoleReader.php";

$consoleReader = new ConsoleReader();

$db_name = '';
$db_user = '';
$db_password = '';

$consoleReader->_readLine();

if($consoleReader->getCurrentFunctionName() === "open") {
    $tmpArgs = $consoleReader->getCurrentArgs();
    $db_name = $tmpArgs['baza'];
    $db_password = $tmpArgs['password'];
    $db_user = $tmpArgs['login'];

    $operator = new OperationManager($db_name, $db_user, $db_password);
    $operator->functionName = $consoleReader->getCurrentFunctionName();
    $operator->execute();
    echo "\n";
}
else
{
    echo "pierwszy argument powinien być open\n";
    exit;
}

while(true) {
    try {
        $consoleReader->_readLine();
        $operator = new OperationManager($db_name, $db_user, $db_password);
        $operator->functionName = $consoleReader->getCurrentFunctionName();
        $operator->args = $consoleReader->getCurrentArgs();
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