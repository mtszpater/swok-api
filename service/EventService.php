<?php
include_once __DIR__ . "/../DatabaseManagerInterface.php";
/**
 * Created by PhpStorm.
 * User: pater
 * Date: 27.05.2017
 * Time: 22:05
 */
class EventService
{
    private $database;

    public function __construct(DatabaseManagerInterface $database)
    {
        $this->database = $database;
    }

    public function createEvent($event_name, $start, $end)
    {
        if ($this->database->isEventNameBusy($event_name)) return false;

        if ($this->database->createEvent($event_name, $start, $end))
            return true;

        return false;
    }

    public function registerUserForEvent($user_login, $event_name)
    {
        if (!$this->database->isEventNameBusy($event_name)) return false;

        if ($this->database->registerUserForEvent($user_login, $event_name))
            return true;

        return false;
    }

}