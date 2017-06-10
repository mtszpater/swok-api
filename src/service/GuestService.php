<?php
include_once __DIR__ . "/../DatabaseManagerInterface.php";
include_once __DIR__ . "/../StatusHandler.php";

/**
 * Class GuestService
 */
abstract class GuestService
{
    /**
     * @var DatabaseManagerInterface
     */
    protected $database;

    /**
     * GuestService constructor.
     * @param DatabaseManagerInterface $database
     */
    public function __construct(DatabaseManagerInterface $database) {
        $this->database = $database;
    }

    /**
     * @param $userLogin
     * @param $limit
     * @return mixed
     */
    public function getUserPlan($userLogin, $limit) {
        return $this->database->getUserPlan($userLogin, $limit);
    }

    /**
     * @param $userLogin
     * @param $userPassword
     * @param $secret
     * @return bool
     */
    public function createOrganizer($userLogin, $userPassword, $secret) {
        if (!($secret === "d8578edf8458ce06fbc5bb76a58c5ca4")) {
            return false;
        }

        if ($this->database->isLoginBusy($userLogin)) {
            return false;
        }

        if ($this->database->registerOrganizer($userLogin, $userPassword)) {
            return true;
        }

        return false;
    }

    /**
     * @param $userLogin
     * @param $userPassword
     * @return bool
     */
    public function registerUser($userLogin, $userPassword) {
        if ($this->database->isLoginBusy($userLogin)) {
            return false;
        }

        if ($this->database->registerUser($userLogin, $userPassword)) {
            return true;
        }

        return false;
    }

    /**
     * @param $userLogin
     * @return mixed
     */
    public function userExists($userLogin) {
        return $this->database->isLoginBusy($userLogin);
    }


}