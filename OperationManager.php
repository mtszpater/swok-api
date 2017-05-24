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

            case "friends":
                $this->_addFriend();
                break;

            case "user_plan":
                $this->_userPlan();
        }
        echo json_encode($this->status);
    }

    public function _loginUser()
    {
        if (isset($this->args['login']) && isset($this->args['password'])) {
            $this->adm = new UserService($this->args['login'], $this->args['password'], $this->database);
        } else {

            if(isset($this->args['login1']))
                $this->adm = new UserService($this->args['login1'], $this->args['password'], $this->database);

            elseif(isset($this->args['login']) && ! isset($this->args['password']))
                $this->adm = new UserService($this->args['login'], '', $this->database);

            else
                $this->adm = new UserService('', '', $this->database);
        }
    }

    private function _createOrganizer()
    {
        $this->status = $this->adm->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret']);
    }

    private function _createUser()
    {
        $this->status = $this->adm->registerUser($this->args['newlogin'], $this->args['newpassword']);
    }

    private function _createEvent()
    {
        $this->status = $this->adm->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp']);
    }

    private function _createTalk()
    {
        // TODO:
//        if($this->args['eventname'] === ' ')
//            $this->args['eventname'] = NULL;

        $this->status = $this->adm->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'], $this->args['room'], $this->args['initial_evaluation'], $this->args['eventname']);
    }

    private function _rejectTalk()
    {
        $this->status = $this->adm->rejectTalk($this->args['talk']);
    }

    private function _registerUserForEvent()
    {
        $this->status = $this->adm->registerUserForEvent($this->args['eventname']);
    }

    private function _checkAttendance()
    {
        $this->status = $this->adm->checkAttendance($this->args['talk']);
    }

    private function _evaluationTalk()
    {
        $this->status = $this->adm->evaluationTalk($this->args['talk'], $this->args['rating']);
    }

    private function _createProposalTalk()
    {
        $this->status = $this->adm->createProposalTalk($this->args['talk'], $this->args['title'], $this->args['start_timestamp']);
    }

    private function _addFriend()
    {
        $this->status = $this->adm->addFriend($this->args['login2']);
    }

    private function _userPlan(){
        $this->status = $this->adm->getUserPlan($this->args['limit']);
    }

}