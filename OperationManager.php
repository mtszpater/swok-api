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


        }
        echo json_encode($this->status);
    }

    private function _createOrganizer()
    {
        $this->adm = new UserService('', '', $this->database);

        if ($this->adm->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _createUser()
    {
        $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);

        if ($this->adm->registerUser($this->args['newlogin'], $this->args['newpassword']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _createEvent()
    {
        $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);

        if ($this->adm->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();

    }

    private function _createTalk()
    {
        $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);

        if ($this->adm->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'],
            $this->args['room'], $this->args['initial_evaluation'], $this->args['eventname'])
        )
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();

    }

    private function _rejectTalk()
    {
        $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);

        if ($this->adm->rejectTalk($this->args['talk']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _registerUserForEvent()
    {
        $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);

        if($this->adm->registerUserForEvent($this->args['eventname']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _checkAttendance()
    {
        $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);

        if($this->adm->checkAttendance($this->args['talk']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _evaluationTalk()
    {
        $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);

        if($this->adm->evaluationTalk($this->args['talk'], $this->args['rating']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _setStatusSuccess()
    {
        $this->status = array('status' => 'OK');
    }

    private function _setStatusError()
    {
        $this->status = array('status' => 'ERROR');
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

