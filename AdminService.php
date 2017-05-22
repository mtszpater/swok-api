<?php
include_once "DatabaseOperation.php";

class AdminService
{

    private $user_login;
    private $user_password;
    private $database;


    public function __construct($user_login, $user_password, DatabaseOperation $database)
    {
        $this->user_login = $user_login;
        $this->user_password = $user_password;
        $this->database = $database;
    }

    public function registerUser($user_login, $user_password){

        if($this->database->isAdmin($this->user_login, $this->user_password) ){

            if($this->database->registerUser($user_login, $user_password))
                echo "Zarejestrowalem uzytkownika";
            else
                echo "Nie moglem go zarejestrowac";

        }

    }




}