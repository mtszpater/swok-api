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


    private function _createUser()
    {
        if($this->adm->registerUser($this->args['newlogin'], $this->args['newpassword']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _createOrganizer()
    {
        if($this->adm->createOrganizer($this->args['login'], $this->args['password'], $this->args['secret']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }

    private function _createEvent()
    {
        if($this->adm->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp']))
            $this->_setStatusSuccess();
        else
            $this->_setStatusError();
    }


    public function execute(){
        switch($this->functionName){

            case "organizer":

                if($this->_checkPerm()) {
                    $this->_createOrganizer();
                }
                else {
                    $this->_setStatusError();
                    exit;
                }

                break;

            case "user":

                if($this->_checkPerm()) {
                    $this->_createUser();
                }
                else {
                    $this->_setStatusError();
                    exit;
                }

                break;

            case "event":

                if($this->_checkPerm()) {
                    $this->_createEvent();
                }
                else {
                    $this->_setStatusError();
                    exit;
                }

                break;




        }
        echo json_encode($this->status);
    }

    private function _checkPerm()
    {
        if($this->adm = new AdminService($this->args['login'], $this->args['password'], $this->database)) return true;

        return false;

    }

    private function _setStatusError()
    {
        $this->status = array('status' => 'ERROR');
    }

    private function _setStatusSuccess()
    {
        $this->status = array('status' => 'OK');
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








//{ "organizer": { "login": "dupa244", "password": "haslo", "secret": "d8578edf8458ce06fbc5bb76a58c5ca4" }}


//{ "organizer": { "login": "dupa", "password": "haslo", "secret": "now22y222" }}
//{ "event": { "login": "dupa", "password": "haslo", "eventname": "jakis22ev3ent23", "start_timestamp": "2004-10-19 10:23:54.000000", "end_timestamp" : "2004-10-20 10:23:54.000000"}}

