<?php

include_once "DatabaseOperation.php";
include_once "StatusHandler.php";

/**
 * Created by PhpStorm.
 * User: pater
 * Date: 25.05.2017
 * Time: 10:51
 */
abstract class GuestService
{
    protected $database;

    public function __construct(DatabaseOperation $database)
    {
        $this->database = $database;
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