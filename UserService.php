<?php
include_once "DatabaseOperation.php";
include_once "StatusHandler.php";
require_once "GuestService.php";

class UserService extends GuestService
{
    private $user_login;
    private $user_password;

    public function __construct(DatabaseOperation $database)
    {
        parent::__construct($database);
    }

    public function createTalk($user_login, $talk_id, $title, $start_timestamp, $room, $initial_evaluation, $event_name)
    {
        if (!$this->database->isAdmin($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if (!$this->database->isLoginBusy($user_login)) return StatusHandler::error("User doesnt exist");
        if (!$this->database->isEventNameBusy($event_name)) return StatusHandler::error("event doesnt exist: $event_name");
        if ($this->database->existsTalk(intval($talk_id))) return StatusHandler::error("busy talk_id: $talk_id");

        $event_start = $this->database->getStartTimeStampOfEvent($event_name);
        if ($event_start >= $start_timestamp) return StatusHandler::error("bad timestamp: $start_timestamp ");

        if ($this->database->createTalk($user_login, intval($talk_id), $title, $start_timestamp, intval($room), $event_name)) {

            $this->evaluationTalk($talk_id, $initial_evaluation);
//            $this->checkAttendance($talk_id);

            if ($this->database->existsProposalTalk(intval($talk_id))) {
                $this->rejectTalk($talk_id);
            }

            return StatusHandler::success();
        }

        return StatusHandler::error("sth went wrong");
    }

    public function rejectTalk($talk_id)
    {
        if (!$this->database->isAdmin($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if (!$this->database->existsProposalTalk($talk_id)) return StatusHandler::error("didnt found talk_id: $talk_id");

        if ($this->database->rejectTalk(intval($talk_id)))
            return StatusHandler::success();

        return StatusHandler::error("sth went wrong");
    }

    public function registerUser($user_login, $user_password)
    {
        if (!$this->database->isAdmin($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if ($this->database->isLoginBusy($user_login)) return StatusHandler::error("busy login: $user_login");

        if ($this->database->registerUser($user_login, $user_password))
            return StatusHandler::success();

        return StatusHandler::error("sth went wrong");
    }

    public function createEvent($event_name, $start, $end)
    {
        if (!$this->database->isAdmin($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if ($this->database->isEventNameBusy($event_name)) return StatusHandler::error("busy eventname: $event_name");

        if ($this->database->createEvent($event_name, $start, $end))
            return StatusHandler::success();

        return StatusHandler::error("sth went wrong");
    }

    public function registerUserForEvent($event_name)
    {
        if (!$this->database->userExists($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if (!$this->database->isEventNameBusy($event_name)) return StatusHandler::error("event doesnt exist: $event_name");

        if ($this->database->registerUserForEvent($this->user_login, $event_name))
            return StatusHandler::success();

        return StatusHandler::error("sth went wrong");
    }

    public function checkAttendance($talk_id)
    {
        if (!$this->database->userExists($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");

        if ($this->database->checkAttendance($this->user_login, intval($talk_id)))
            return StatusHandler::success();

        return StatusHandler::error("sth went wrong");
    }

    public function evaluationTalk($talk_id, $rate)
    {
        if (!$this->database->userExists($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if (!$this->database->existsTalk($talk_id)) return StatusHandler::error("talk doesnt exist: $talk_id");

        if ($this->database->evaluationTalk($this->user_login, intval($talk_id), intval($rate)))
            return StatusHandler::success();

        return StatusHandler::error("sth went wrong");
    }

    public function createProposalTalk($talk_id, $title, $start_timestamp)
    {
        if (!$this->database->userExists($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if ($this->database->existsProposalTalk($talk_id)) return StatusHandler::error("busy proposal_talk_id: $talk_id");
        if ($this->database->existsTalk($talk_id)) return StatusHandler::error("busy talk_id ( in column talk ): $talk_id");

        if ($this->database->createProposalTalk($this->user_login, intval($talk_id), $title, $start_timestamp)) {
            return StatusHandler::success();
        }

        return StatusHandler::error("sth went wrong");
    }

    public function addFriend($user_login)
    {
        if (!$this->database->userExists($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        if ($user_login === $this->user_login) return StatusHandler::error("Im my friend :D");
        if (!$this->database->isLoginBusy($user_login)) return StatusHandler::error("your friend doesnt exist : $user_login");

        if ($this->database->addFriend($this->user_login, $user_login)) {
            return StatusHandler::success();
        }

        return StatusHandler::error("sth went wrong");
    }

    public function attendedTalks()
    {
        if (!$this->database->userExists($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");

        return StatusHandler::success($this->database->getAttendedTalks($this->user_login));
    }

    public function abandonedTalks($limit)
    {
// (*O) abandoned_talks <login> <password>  <limit> // zwraca listę referatów posortowaną malejąco wg liczby uczestników <number> zarejestrowanych na wydarzenie obejmujące referat, którzy nie byli na tym referacie obecni, wypisuje pierwsze <limit> referatów, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <start_timestamp> <title> <room> <number>
        return StatusHandler::not_implemented();
    }

    public function rejectedTalks()
    {
// (U/O) rejected_talks <login> <password> // jeśli wywołujący ma uprawnienia organizatora zwraca listę wszystkich odrzuconych referatów spontanicznych, w przeciwnym przypadku listę odrzuconych referatów wywołującego ją uczestnika
//  <talk> <speakerlogin> <start_timestamp> <title>
        return StatusHandler::not_implemented();
    }

    public function proposal()
    {
        if (!$this->database->isAdmin($this->user_login, $this->user_password)) return StatusHandler::error("Permission denied");
        return StatusHandler::success($this->database->getProposalTalks());
    }

    public function friendsTalks($start_timestamp, $end_timestamp, $limit)
    {
//  (U) friends_talks <login> <password> <start_timestamp> <end_timestamp> <limit> // lista referatów  rozpoczynających się w podanym przedziale czasowym wygłaszanych przez znajomych danego uczestnika posortowana wg czasu rozpoczęcia, wypisuje pierwsze <limit> referatów, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <speakerlogin> <start_timestamp> <title> <room>
        return StatusHandler::not_implemented();
    }

    public function friendsEvents($event)
    {
//  (U) friends_events <login> <password> <event> // lista znajomych uczestniczących w danym wydarzeniu
//  <login> <event> <friendlogin>
        return StatusHandler::not_implemented();
    }

    public function recommendedTalks($start_timestamp, $end_timestamp, $limit)
    {
//  (U) recommended_talks <login> <password> <start_timestamp> <end_timestamp> <limit> // zwraca referaty rozpoczynające się w podanym przedziale czasowym, które mogą zainteresować danego uczestnika (zaproponuj parametr <score> obliczany na podstawie dostępnych danych – ocen, obecności, znajomości itp.), wypisuje pierwsze <limit> referatów wg nalepszego <score>, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <speakerlogin> <start_timestamp> <title> <room> <score>
        return StatusHandler::not_implemented();
    }

    public function setUserPassword($user_password)
    {
        $this->user_password = $user_password;
    }

    public function setUserLogin($user_login)
    {
        $this->user_login = $user_login;
    }



}