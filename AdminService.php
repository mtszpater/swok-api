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
        $this->user_login = $user_login;
        $this->user_password = $user_password;


    }

    public function registerUser($user_login, $user_password){

        //TODO sql injection
        if (! $this->database->isAdmin($this->user_login, $this->user_password)) {
            return false;
        }

        if( empty($user_password) || empty($user_login)){
            return false;
        }

        if( $this->database->isLoginBusy($user_login) ){
            return false;
        }


        if($this->database->registerUser($user_login, $user_password)) {
            return true;
        }

        return false;
    }

    public function createEvent($event_name, $start, $end){

        if (! $this->database->isAdmin($this->user_login, $this->user_password)) {
            return false;
        }

        if($this->database->createEvent($event_name, $start, $end)){
            return true;
        }

        return false;

    }







}