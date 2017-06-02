<?php
include_once "ApiController.php";
include_once "ConsoleReader.php";

class App
{
    private $db_name;
    private $db_user;
    private $db_password;
    private $firstLine = true;
    /**
     * @Var ConsoleReader
     */
    private $consoleReader;
    /**
     * @var ApiController
     */
    private $apiController;

    public function __construct()
    {
        $this->consoleReader = new ConsoleReader();
    }

    public function readLine()
    {
        if ($this->firstLine)
            $this->readFirstLine();
        else
            $this->readNextLine();

    }

    public function execute()
    {
        $this->apiController->execute();
        echo "\n";
    }

    private function readFirstLine()
    {
        $this->consoleReader->_readLine();
        if ($this->consoleReader->getCurrentFunctionName() === "open") {
            $this->saveDataToDb();
            $this->initializeOperationManager();
            $this->firstLine = false;
        } else {
            throw new Exception("Bad first argument, should be open");
        }
    }

    private function readNextLine()
    {
        $this->consoleReader->_readLine();
        $this->initializeOperationManager();
    }

    private function initializeOperationManager()
    {
        $this->apiController = new ApiController($this->db_name, $this->db_user, $this->db_password);
        $this->apiController->functionName = $this->consoleReader->getCurrentFunctionName();
        $this->apiController->args = $this->consoleReader->getCurrentArgs();
    }

    private function saveDataToDb()
    {
        $tmpArgs = $this->consoleReader->getCurrentArgs();
        $this->db_name = $tmpArgs['baza'];
        $this->db_password = $tmpArgs['password'];
        $this->db_user = $tmpArgs['login'];
    }


}
