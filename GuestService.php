<?php
include_once "DatabaseOperation.php";
include_once "StatusHandler.php";

abstract class GuestService
{
    protected $database;

    public function __construct(DatabaseOperation $database)
    {
        $this->database = $database;
    }

    public function createOrganizer($user_login, $user_password, $secret)
    {
        if (!($secret === "d8578edf8458ce06fbc5bb76a58c5ca4")) return StatusHandler::error("Wrong secret key");
        if ($this->database->isLoginBusy($user_login)) return StatusHandler::error("busy login: $user_login");

        if ($this->database->registerOrganizer($user_login, $user_password))
            return StatusHandler::success();

        return StatusHandler::error("sth went wrong");
    }

    public function getUserPlan($user_login, $limit)
    {
        if (!$this->database->isLoginBusy($user_login)) return StatusHandler::error("user doesnt exist : $user_login");

        return StatusHandler::success(true, $this->database->getUserPlan($user_login, $limit));
    }

    public function getDayPlan($timestamp)
    {
        return StatusHandler::success(true, $this->database->getDayPlan($timestamp));
    }

    public function getBestTalks($start_timestamp, $end_timestamp, $limit, $all)
    {
        return StatusHandler::success(true, $this->database->getBestTalks($start_timestamp, $end_timestamp, $limit, $all));
    }

    public function getMostPopularTalks($start_timestamp, $end_timestamp, $limit)
    {
        return StatusHandler::success(true, $this->database->getMostPopularTalks($start_timestamp, $end_timestamp, $limit));
    }

}