<?php
require_once "DatabaseManager.php";
require_once "UserService.php";
require_once "TalkService.php";
require_once "EventService.php";

class OperationManager
{
    public $functionName;
    public $args = array();
    private $database;
    /**
     * @var UserService
     */
    private $adm;
    /**
     * @var AuthClass
     */
    private $auth;
    /**
     * @var TalkService
     */
    private $talk;
    /**
     * @var EventService
     */
    private $event;
    private $status;


    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function execute(){
        $this->_prepareArguments();
        $this->_init();

        switch($this->functionName){
            case "open":
                $this->_open();
                break;

            case "organizer":
                $this->_organizer();
                break;

            case "user":
                $this->_user();
                break;

            case "event":
                $this->_event();
                break;

            case "talk":
                $this->_talk();
                break;

            case "reject":
                $this->_reject();
                break;

            case "register_user_for_event":
                $this->_registerUserForEvent();
                break;

            case "attendance":
                $this->_attendance();
                break;

            case "evaluation":
                $this->_evaluation();
                break;

            case "proposal":
                $this->_proposal();
                break;

            case "proposals":
                $this->_proposals();
                break;

            case "friends":
                $this->_friends();
                break;

            case "user_plan":
                $this->_userPlan();
                break;

            case "day_plan":
                $this->_dayPlan();
                break;

            case "best_talks":
                $this->_bestTalks();
                break;

            case "most_popular_talks":
                $this->_mostPopularTalks();
                break;

            case "attended_talks":
                $this->_attendedTalks();
                break;

            case "abandoned_talks":
                $this->_abandonedTalks();
                break;

            case "recently_added_talks":
                $this->_recentlyAddedTalks();
                break;

            case "rejected_talks":
                $this->_rejectedTalks();
                break;

            case "friends_talks":
                $this->_friendsTalks();
                break;

            case "friends_events";
                $this->_friendsEvents();
                break;

            case "recommended_talks";
                $this->status = StatusHandler::success($this->talk->recommendedTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
                break;

        }
        echo json_encode($this->status);
    }

    private function _prepareArguments()
    {
        foreach ($this->args as $key => $value) {
            $this->args['key'] = $value;
        }
    }

    private function _init()
    {
        $user_password = '';
        $user_login = '';

        if(isset($this->args['password'])) $user_password = $this->args['password'];
        if(isset($this->args['login'])) $user_login = $this->args['login'];
        if(isset($this->args['login1']))  $user_login = $this->args['login1'];

        /** Nie fajnie, że jedna komenda w API inny argument loginu :c */

        $this->adm = new UserService($user_login, $user_password, $this->database);
        $this->auth = new AuthClass($user_login, $user_password, $this->database);
        $this->talk = new TalkService($this->database);
        $this->event = new EventService($this->database);
    }

    private function _open()
    {
//        TODO: ma wywalac blad przy zlym polaczeniu
        $this->database = new DatabaseManager($this->args['baza'], $this->args['login'], $this->args['password']);
        $this->status = StatusHandler::success();
    }

    private function _organizer()
    {
        if($this->auth->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();
    }

    private function _user()
    {
        if (!$this->auth->isAdmin()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->auth->registerUser($this->args['newlogin'], $this->args['newpassword']))
            $this->status = StatusHandler::success();
    }

    private function _event()
    {
        if (!$this->auth->isAdmin()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->event->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();
    }

    private function _talk()
    {
//TODO: event_name może byc pusty
        if (!$this->auth->isAdmin()) {
            $this->status = StatusHandler::error();
            return;
        }

        if(!$this->auth->userExists($this->args['speakerlogin'])){
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->talk->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'], $this->args['room'], $this->args['initial_evaluation'], $this->args['eventname']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();

    }

    private function _reject()
    {
        if (!$this->auth->isAdmin()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->talk->rejectTalk($this->args['talk']))
                $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();

    }

    private function _registerUserForEvent()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->event->registerUserForEvent($this->auth->getUserLogin(), $this->args['eventname']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();
    }

    private function _attendance()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->talk->checkAttendance($this->auth->getUserLogin(), $this->args['talk']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();
    }

    private function _evaluation()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->talk->evaluationTalk($this->auth->getUserLogin(), $this->args['talk'], $this->args['rating']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();
    }

    private function _proposal()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->talk->createProposalTalk($this->auth->getUserLogin(), $this->args['talk'], $this->args['title'], $this->args['start_timestamp']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();
    }

    private function _proposals()
    {
        if (!$this->auth->isAdmin()) {
            $this->status = StatusHandler::error();
            return;
        }

        $this->status = StatusHandler::success($this->talk->proposal());
    }

    private function _friends()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        if ($this->adm->addFriend($this->args['login2']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error();

    }

    private function _userPlan()
    {
        $this->status = StatusHandler::success($this->adm->getUserPlan($this->args['login'], $this->args['limit']));
    }

    private function _dayPlan()
    {
        $this->status = StatusHandler::success($this->adm->getDayPlan($this->args['timestamp']));
    }

    private function _bestTalks()
    {
        $this->status = StatusHandler::success($this->adm->getBestTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit'], $this->args['all']));
    }

    private function _mostPopularTalks()
    {
        $this->status = StatusHandler::success($this->adm->getMostPopularTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function _attendedTalks()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        $this->status = StatusHandler::success($this->talk->attendedTalks($this->auth->getUserLogin()));
    }

    private function _abandonedTalks()
    {
        if (!$this->auth->isAdmin()) {
            $this->status = StatusHandler::error();
            return;
        }

        $this->status = StatusHandler::success($this->talk->abandonedTalks($this->args['limit']));
    }

    private function _recentlyAddedTalks()
    {
        $this->status = $this->adm->getRecentlAddedTalks($this->args['limit']);
    }

    private function _rejectedTalks()
    {
        if ($this->auth->isAdmin())
            $this->status = StatusHandler::success($this->talk->getAllRejectedTalks());
        elseif ($this->auth->isLogged())
            $this->status = StatusHandler::success($this->talk->getAllRejectedTalksForUser($this->auth->getUserLogin()));
        else
            $this->status = StatusHandler::error();
    }

    private function _friendsTalks()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        $this->status = StatusHandler::success($this->talk->friendsTalks($this->auth->getUserLogin(), $this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function _friendsEvents()
    {
        if (!$this->auth->isLogged()) {
            $this->status = StatusHandler::error();
            return;
        }

        $this->status = StatusHandler::success($this->adm->friendsEvents($this->args['event']));
    }



}