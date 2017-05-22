<?php
include_once "DatabaseOperation.php";

class AdminService
{

    private $user_login;
    private $user_password;
    private $database;


    public function __construct($user_login, $user_password, DatabaseOperation $database)
    {
        $this->database = $database;

        if( ! $this->database->isAdmin($user_login, $user_password) ){
            echo "Nie masz uprawnień";
            return false;

            //TODO WYWALIĆ JAKIŚ BŁĄD TUTAJ JAK NIE MA PRAWNIEN
        }

        $this->user_login = $user_login;
        $this->user_password = $user_password;


    }

    public function registerUser($user_login, $user_password){

        if( empty($user_password) || empty($user_login)){
            echo "Puste pola";
            return false;
        }

        if( $this->database->isLoginBusy($user_login) ){
            echo "Login zajęty";
            return false;
        }


        if($this->database->registerUser($user_login, $user_password)) {
            echo "Zarejestrowalem uzytkownika";
            return true;
        }


        echo "Nie moglem go zarejestrowac";
        return false;

    }




}