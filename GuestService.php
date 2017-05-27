<?php
include_once "DatabaseManagerInterface.php";
include_once "StatusHandler.php";

abstract class GuestService
{
    protected $database;

    public function __construct(DatabaseManagerInterface $database)
    {
        $this->database = $database;
    }

    public function getUserPlan($user_login, $limit)
    {
//TODO: sprawdzic czy istnieje $user_login
        return $this->database->getUserPlan($user_login, $limit);
    }

    public function getDayPlan($timestamp)
    {
        return $this->database->getDayPlan($timestamp);
    }

    public function getBestTalks($start_timestamp, $end_timestamp, $limit, $all)
    {
        return $this->database->getBestTalks($start_timestamp, $end_timestamp, $limit, $all);
    }

    public function getMostPopularTalks($start_timestamp, $end_timestamp, $limit)
    {
        return $this->database->getMostPopularTalks($start_timestamp, $end_timestamp, $limit);
    }

    public function getRecentlAddedTalks($limit)
    {
//  (N) recently_added_talks <limit> // zwraca listę ostatnio zarejestrowanych referatów, wypisuje ostatnie <limit> referatów wg daty zarejestrowania, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <speakerlogin> <start_timestamp> <title> <room>
        return StatusHandler::not_implemented();
    }

}