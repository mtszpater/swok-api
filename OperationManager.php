<?php
require_once "DatabaseManager.php";
require_once "AdminService.php";

/**
 * Created by PhpStorm.
 * User: pater
 * Date: 23.05.2017
 * Time: 15:59
 */
class OperationManager
{
    public $functionName;
    public $args = array();
    private $database;
    private $adm;
    private $status;

    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function execute(){
        $this->_loginUser();

        switch($this->functionName){
            case "organizer":
                $this->_createOrganizer();
                break;

            case "user":
                $this->_createUser();
                break;

            case "event":
                $this->_createEvent();
                break;

            case "talk":
                $this->_createTalk();
                break;

            case "reject":
                $this->_rejectTalk();
                break;

            case "register_user_for_event":
                $this->_registerUserForEvent();
                break;

            case "attendance":
                $this->_checkAttendance();
                break;

            case "evaluation":
                $this->_evaluationTalk();
                break;

            case "proposal":
                $this->_createProposalTalk();
                break;
        }
        echo json_encode($this->status);
    }

    public function _loginUser()
    {
        if (isset($this->args['login']) && isset($this->args['password'])) {
            $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);
        } else {
            $this->adm = new UserService('', '', $this->database);
        }
    }

    private function _createOrganizer()
    {
        $this->_setStatus($this->adm->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret']));
    }

    private function _createUser()
    {
        $this->_setStatus($this->adm->registerUser($this->args['newlogin'], $this->args['newpassword']));
    }

    private function _createEvent()
    {
        $this->_setStatus($this->adm->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp']));
    }

    private function _createTalk()
    {
        $this->_setStatus($this->adm->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'], $this->args['room'], $this->args['initial_evaluation'], $this->args['eventname']));
    }

    private function _rejectTalk()
    {
        $this->_setStatus($this->adm->rejectTalk($this->args['talk']));
    }

    private function _registerUserForEvent()
    {
        $this->_setStatus($this->adm->registerUserForEvent($this->args['eventname']));

    }

    private function _checkAttendance()
    {
        $this->_setStatus($this->adm->checkAttendance($this->args['talk']));
    }

    private function _evaluationTalk()
    {
        $this->_setStatus($this->adm->evaluationTalk($this->args['talk'], $this->args['rating']));
    }

    private function _createProposalTalk()
    {
        $this->_setStatus($this->adm->createProposalTalk($this->args['login'], $this->args['talk'], $this->args['title']));
    }

    private function _setStatus($ask){
        if ($ask === "OK")
            $this->_setStatusSuccess();
        else
            $this->_setStatusError($ask);
    }

    private function _setStatusSuccess()
    {
        $this->status = array('status' => 'OK');
    }

    private function _setStatusError($arg)
    {
        $this->status = array('status' => 'ERROR', 'data' => $arg);
    }

    private function _setStatusErrorWithArgs()
    {
        $this->status = array('status' => 'ERROR', 'data' => $this->args);
    }

    private function _setStatusSuccessWithArgs()
    {
        $this->status = array('status' => 'OK', 'data' => $this->args);
    }



}

// { "reject": { "login": "dupa", "password": "haslo", "talk" : "0" }}

// { "talk": { "login": "dupa", "password": "haslo", "speakerlogin": "dupa244", "talk": "5", "title": "tytualsdasd", "start_timestamp": "2004-10-19 10:23:54.000000", "room": "244", "initial_evaluation": "5", "eventname": "jakis22ev3ent23" }}
//{ "talk": { "login": "dupa", "password": "haslo", "speakerlogin": "dupa2445", "talk": "6", "title": "tytualsdasd", "start_timestamp": "2004-10-19 10:23:54.000000", "room": "244", "initial_evaluation": "5", "eventname": "jakis22ev3ent23" }}

//{ "organizer": { "login": "", "password": "haslo", "secret": "d8578edf8458ce06fbc5bb76a58c5ca4" }}


//{ "organizer": { "login": "dupa", "password": "haslo", "secret": "now22y222" }}
//{ "event": { "login": "dupa", "password": "haslo", "eventname": "jakis22ev3ent23", "start_timestamp": "2004-10-19 10:23:54.000000", "end_timestamp" : "2004-10-20 10:23:54.000000"}}

