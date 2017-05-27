<?php
include_once "DatabaseManagerInterface.php";
/**
 * Created by PhpStorm.
 * User: pater
 * Date: 27.05.2017
 * Time: 21:25
 */
class TalkService
{
    /**
     * @var DatabaseManagerInterface
     */
    private $database;

    /**
     * TalkService constructor.
     * @param DatabaseManagerInterface $database
     */
    public function __construct(DatabaseManagerInterface $database)
    {
        $this->database = $database;
    }

    public function createTalk($user_login, $talk_id, $title, $start_timestamp, $room, $initial_evaluation, $event_name)
    {
        if (!$this->database->isEventNameBusy($event_name)) return false;
        if ($this->database->existsTalk(intval($talk_id))) return false;

        $event_start = $this->database->getStartTimeStampOfEvent($event_name);
        if ($event_start >= $start_timestamp) return false;

        if ($this->database->createTalk($user_login, intval($talk_id), $title, $start_timestamp, intval($room), $event_name)) {

            if ($this->database->existsProposalTalk(intval($talk_id))) {
                $this->rejectTalk($talk_id);
            }
            $this->database->evaluationTalk($user_login, $talk_id, $initial_evaluation, true);
            return true;
        }

        return false;
    }

    public function rejectTalk($talk_id)
    {
        if (!$this->database->existsProposalTalk($talk_id)) return false;

        if ($this->database->rejectTalk(intval($talk_id)))
                return true;

        return false;
    }

    public function checkAttendance($user_login, $talk_id)
    {
        if ($this->database->checkAttendance($user_login, intval($talk_id)))
                return true;

        return false;
    }

    public function evaluationTalk($user_login, $talk_id, $rate)
    {
        if (!$this->database->existsTalk($talk_id)) return false;

        if ($this->database->evaluationTalk($user_login, intval($talk_id), intval($rate)))
                return true;

        return false;
    }

    public function createProposalTalk($user_login, $talk_id, $title, $start_timestamp)
    {
        if ($this->database->existsProposalTalk($talk_id)) return false;
        if ($this->database->existsTalk($talk_id)) return false;

        if ($this->database->createProposalTalk($user_login, intval($talk_id), $title, $start_timestamp))
                return true;

        return false;
    }

    public function attendedTalks($user_login)
    {
        return $this->database->getAttendedTalks($user_login);
    }

    public function abandonedTalks($limit)
    {
        return $this->database->getAbandonedTalks($limit);
    }

    public function getAllRejectedTalks()
    {
        return $this->database->getAllRejectedTalks();
    }

    public function getAllRejectedTalksForUser($user_login)
    {
        return $this->database->getAllRejectedTalksForUser($user_login);
    }

    public function proposal()
    {
        return $this->database->getProposalTalks();
    }

    public function friendsTalks($user_login, $start_timestamp, $end_timestamp, $limit)
    {
        return $this->database->getFriendsTalks($user_login, $start_timestamp, $end_timestamp, $limit);
    }

    public function recommendedTalks($start_timestamp, $end_timestamp, $limit)
    {
//  (U) recommended_talks <login> <password> <start_timestamp> <end_timestamp> <limit> // zwraca referaty rozpoczynające się w podanym przedziale czasowym, które mogą zainteresować danego uczestnika (zaproponuj parametr <score> obliczany na podstawie dostępnych danych – ocen, obecności, znajomości itp.), wypisuje pierwsze <limit> referatów wg nalepszego <score>, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <speakerlogin> <start_timestamp> <title> <room> <score>
        return StatusHandler::not_implemented();
    }

}