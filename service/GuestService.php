<?php
include_once __DIR__ . "/../DatabaseManagerInterface.php";
include_once __DIR__ . "/../StatusHandler.php";

abstract class GuestService
{
    protected $database;

    public function __construct(DatabaseManagerInterface $database)
    {
        $this->database = $database;
    }

    public function getUserPlan($user_login, $limit)
    {
        return $this->database->getUserPlan($user_login, $limit);
    }

    public function createOrganizer($user_login, $user_password, $secret)
    {
        if (!($secret === "d8578edf8458ce06fbc5bb76a58c5ca4")) return false;
        if ($this->database->isLoginBusy($user_login)) return false;

        if ($this->database->registerOrganizer($user_login, $user_password))
            return true;

        return false;
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


}