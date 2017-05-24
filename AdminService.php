<?php
include_once "DatabaseOperation.php";

class UserService
{

    private $user_login;
    private $user_password;
    private $database;


    public function __construct($user_login = '', $user_password = '', DatabaseOperation $database)
    {
        $this->database = $database;
        $this->user_login = $user_login;
        $this->user_password = $user_password;
    }

    public function createTalk($user_login, $talk_id, $title, $start_timestamp, $room, $initial_evaluation, $event_name)
    {
        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return array("info" => "Permission denied");

        if( $this->database->existsProposalTalk($talk_id) )
            return array("info" => "busy talk_id: $talk_id");

        if($this->database->createTalk($user_login, intval($talk_id), $title, $start_timestamp, intval($room), intval($initial_evaluation), $event_name)) {
            $this->evaluationTalk($talk_id, $initial_evaluation);
            return "OK";
        }

        return array("info" => "sth went wrong");
    }

    public function rejectTalk($talk_id)
    {
        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return array("info" => "Permission denied");

        if(! $this->database->existsProposalTalk($talk_id) )
            return array("info" => "didnt found talk_id: $talk_id");

        if($this->database->rejectTalk($talk_id))
            return "OK";

        return array("info" => "sth went wrong");
    }

    public function registerUser($user_login, $user_password)
    {
        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return array("info" => "Permission denied");

        if( $this->database->isLoginBusy($user_login) )
            return array("info" => "busy login: $user_login");

        if($this->database->registerUser($user_login, $user_password))
            return "OK";

        return array("info" => "sth went wrong");
    }

    public function createEvent($event_name, $start, $end)
    {
        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return array("info" => "Permission denied");

        if($this->database->isEventNameBusy($event_name) )
            return array("info" => "busy eventname: $event_name");

        if($this->database->createEvent($event_name, $start, $end))
            return "OK";

        return array("info" => "sth went wrong");
    }

    public function createOrganizer($user_login, $user_password, $secret)
    {
        if( ! ($secret === "d8578edf8458ce06fbc5bb76a58c5ca4") )
            return array("info" => "Wrong secret key");

        if( $this->database->isLoginBusy($user_login) )
            return array("info" => "busy login: $user_login");

        if($this->database->registerOrganizer($user_login, $user_password))
            return "OK";

        return array("info" => "sth went wrong");
    }

    public function registerUserForEvent($event_name)
    {
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return array("info" => "Permission denied");

        if($this->database->registerUserForEvent($this->user_login, $event_name))
            return "OK";

        return array("info" => "sth went wrong");
    }

    public function checkAttendance($talk_id)
    {
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return array("info" => "Permission denied");

        if($this->database->checkAttendance($this->user_login, $talk_id))
            return "OK";

        return array("info" => "sth went wrong");
    }

    public function evaluationTalk($talk_id, $rate){
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return array("info" => "Permission denied");

        if( ! $this->database->existsTalk($talk_id) )
            return array("info" => "talk doesnt exist: $talk_id");

        if($this->database->evaluationTalk($this->user_login, $talk_id, $rate))
            return "OK";

        return array("info" => "sth went wrong");
    }

    public function createProposalTalk($talk_id, $title, $start_timestamp){
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return array("info" => "Permission denied");

        if( $this->database->existsProposalTalk($talk_id) )
            return array("info" => "busy proposal_talk_id: $talk_id");

        if($this->database->createProposalTalk($this->user_login, intval($talk_id), $title, $start_timestamp)) {
            return "OK";
        }

        return array("info" => "sth went wrong");
    }

}