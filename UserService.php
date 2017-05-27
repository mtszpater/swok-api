<?php
include_once "DatabaseManagerInterface.php";
include_once "StatusHandler.php";
require_once "GuestService.php";
include_once "AuthClass.php";

class UserService extends GuestService
{
    private $user_login;
    private $user_password;

    public function __construct($user_login, $user_password, DatabaseManagerInterface $database)
    {
        $this->user_login = $user_login;
        $this->user_password = $user_password;

        parent::__construct($database);
    }

    public function addFriend($user_login)
    {
        if ($user_login === $this->user_login) return false;
        if (!$this->database->isLoginBusy($user_login)) return false;

        if ($this->database->addFriend($this->user_login, $user_login))
            return true;

        return false;
    }

    public function friendsEvents($event)
    {
        return $this->database->getFriendsEvents($this->user_login, $event);
    }


}