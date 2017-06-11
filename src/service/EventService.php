<?php
/**
 * Class EventService
 */
class EventService
{
    /**
     * @var DatabaseManagerInterface
     */
    private $database;

    /**
     * EventService constructor.
     * @param DatabaseManagerInterface $database
     */
    public function __construct(DatabaseManagerInterface $database) {
        $this->database = $database;
    }

    /**
     * @param $eventName
     * @param $eventStart
     * @param $eventEnd
     * @return bool
     */
    public function createEvent($eventName, $eventStart, $eventEnd) {
        if ($this->database->isEventNameBusy($eventName)) {
            return false;
        }

        if ($this->database->createEvent($eventName, $eventStart, $eventEnd)) {
            return true;
        }

        return false;
    }

    /**
     * @param $userLogin
     * @param $eventName
     * @return bool
     */
    public function registerUserForEvent($userLogin, $eventName) {
        if (!$this->database->isEventNameBusy($eventName)) {
            return false;
        }

        if ($this->database->registerUserForEvent($userLogin, $eventName)) {
            return true;
        }

        return false;
    }
}