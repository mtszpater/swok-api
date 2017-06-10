<?php
include_once __DIR__ . "/../DatabaseManagerInterface.php";
require_once "GuestService.php";

/**
 * Class UserService
 */
class UserService extends GuestService
{
    /**
     * @var DatabaseManagerInterface
     */
    private $userLogin;
    /**
     * @var
     */
    private $userPassword;

    /**
     * UserService constructor.
     * @param DatabaseManagerInterface $userLogin
     * @param $userPassword
     * @param DatabaseManagerInterface $database
     */
    public function __construct($userLogin, $userPassword, DatabaseManagerInterface $database) {
        $this->userLogin = $userLogin;
        $this->userPassword = $userPassword;

        parent::__construct($database);
    }

    /**
     * @param $userLogin
     * @return bool
     */
    public function addFriend($userLogin) {
        if ($userLogin === $this->userLogin) {
            return false;
        }

        if (!$this->database->isLoginBusy($userLogin)) {
            return false;
        }

        if ($this->database->addFriend($this->userLogin, $userLogin)) {
            return true;
        }

        return false;
    }

    /**
     * @param $eventName
     * @return mixed
     */
    public function friendsEvents($eventName) {
        return $this->database->getFriendsEvents($this->userLogin, $eventName);
    }

    /**
     * @return bool
     */
    public function isLogged() {
        if(!$this->database->userExists($this->userLogin, $this->userPassword)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isAdmin() {
        if (!$this->database->isAdmin($this->userLogin, $this->userPassword)) {
            return false;
        }

        return true;
    }

    /**
     * @return DatabaseManagerInterface
     */
    public function getUserLogin() {
        return $this->userLogin;
    }


}