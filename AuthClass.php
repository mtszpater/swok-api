<?php

include_once "DatabaseManagerInterface.php";
/**
 * Created by PhpStorm.
 * User: pater
 * Date: 27.05.2017
 * Time: 20:24
 */
class AuthClass
{
    private $user_login;
    private $user_password;
    private $database;

    public function __construct($user_login, $user_password, DatabaseManagerInterface $database)
    {
        $this->user_login = $user_login;
        $this->user_password = $user_password;
        $this->database = $database;
    }

    public function isLogged()
    {
        if(!$this->database->userExists($this->user_login, $this->user_password)) return false;
        return true;
    }

    public  function isAdmin()
    {
        if (!$this->database->isAdmin($this->user_login, $this->user_password)) return false;
        return true;
    }

    public function registerUser($user_login, $user_password)
    {
        if ($this->database->isLoginBusy($user_login)) return false;
        if ($this->database->registerUser($user_login, $user_password))
            return true;

        return false;
    }

    public function userExists($user_login){
        return $this->database->isLoginBusy($user_login);
    }

    public function getUserLogin()
    {
        return $this->user_login;
    }

    public function createOrganizer($user_login, $user_password, $secret)
    {
        if (!($secret === "d8578edf8458ce06fbc5bb76a58c5ca4")) return false;
        if ($this->database->isLoginBusy($user_login)) return false;

        if ($this->database->registerOrganizer($user_login, $user_password))
            return true;

        return false;
    }


}