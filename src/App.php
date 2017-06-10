<?php
include_once "ApiController.php";
include_once "ConsoleReader.php";

/**
 * Class App
 */
class App
{
    /**
     * @var
     */
    private $dbName;
    /**
     * @var
     */
    private $dbUser;
    /**
     * @var
     */
    private $dbPassword;
    /**
     * @var bool
     */
    private $firstLine = true;
    /**
     * @Var ConsoleReader
     */
    private $consoleReader;
    /**
     * @var ApiController
     */
    private $apiController;

    /**
     * App constructor.
     */
    public function __construct() {
        $this->consoleReader = new ConsoleReader();
    }

    public function readLine() {
        if ($this->firstLine) {
            $this->readFirstLine();
        }
        else {
            $this->readNextLine();
        }
    }

    private function readFirstLine() {
        $this->consoleReader->readLine();

        if ($this->consoleReader->getCurrentFunctionName() === "open") {
            $this->setDatabaseProperties();
            $this->initializeApiController();
            $this->firstLine = false;
        } else {
            throw new Exception("Bad first argument, should be open");
        }
    }

    private function readNextLine() {
        $this->consoleReader->readLine();
        $this->initializeApiController();
    }

    private function setDatabaseProperties() {
        $tmpArgs = $this->consoleReader->getCurrentArgs();
        $this->dbName = $tmpArgs['baza'];
        $this->dbPassword = $tmpArgs['password'];
        $this->dbUser = $tmpArgs['login'];
    }

    private function initializeApiController() {
        $this->apiController = new ApiController($this->dbName, $this->dbUser, $this->dbPassword);
        $this->apiController->functionName = $this->consoleReader->getCurrentFunctionName();
        $this->apiController->args = $this->consoleReader->getCurrentArgs();
    }

    public function execute() {
        $this->apiController->execute();
        echo "\n";
    }

}
