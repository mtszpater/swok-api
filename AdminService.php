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

    public function createTalk($user_login, $talk_id, $title, $start_timestamp, $room, $initial_evaluation, $event_name){
//        TODO: Zabezpieczenia (uzytkownik nie istnieje, id zajęte, ogolnie wszystko xd)
        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return false;

        if( $this->database->existsProposalTalk($talk_id) )
            return false;

        if($this->database->createTalk($user_login, intval($talk_id), $title, $start_timestamp, intval($room), intval($initial_evaluation), $event_name)) {
            $this->evaluationTalk($talk_id, $initial_evaluation);
            return true;
        }

        return false;
    }

    public function rejectTalk($talk_id)
    {
        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return false;

        if(! $this->database->existsProposalTalk($talk_id) )
            return false;

        if($this->database->rejectTalk($talk_id)) return true;

        return false;
    }

    public function registerUser($user_login, $user_password){
        //TODO sql injection
        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return false;

        if( $this->database->isLoginBusy($user_login) )
            return false;

        if($this->database->registerUser($user_login, $user_password))
            return true;

        return false;
    }

    public function createEvent($event_name, $start, $end){

        if (! $this->database->isAdmin($this->user_login, $this->user_password))
            return false;

        if($this->database->isEventNameBusy($event_name) )
            return false;

        if($this->database->createEvent($event_name, $start, $end))
            return true;

        return false;
    }

    public function createOrganizer($user_login, $user_password, $secret)
    {
        if( ! ($secret === "d8578edf8458ce06fbc5bb76a58c5ca4") )
            return false;

        if( $this->database->isLoginBusy($user_login) )
            return false;

        if($this->database->registerOrganizer($user_login, $user_password))
            return true;

        return false;
    }

    public function registerUserForEvent($event_name)
    {
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return false;

        if($this->database->registerUserForEvent($this->user_login, $event_name))
            return true;

        return false;
    }

    public function checkAttendance($talk_id)
    {
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return false;

        if($this->database->checkAttendance($this->user_login, $talk_id))
            return true;

        return false;
    }

    public function evaluationTalk($talk_id, $rate){
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return false;

        if( ! $this->database->existsTalk($talk_id) )
            return false;

        if($this->database->evaluationTalk($this->user_login, $talk_id, $rate))
            return true;

        return false;

    }

    public function createProposalTalk($talk_id, $title, $start_timestamp){
        if( ! $this->database->userExists($this->user_login, $this->user_password) )
            return false;

        if( $this->database->existsProposalTalk($talk_id) )
            return false;

        if($this->database->createProposalTalk($this->user_login, intval($talk_id), $title, $start_timestamp)) {
            return true;
        }

        return false;
    }

}