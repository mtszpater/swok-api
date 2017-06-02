<?php
require_once "DatabaseManager.php";
require_once "service/UserService.php";
require_once "service/TalkService.php";
require_once "service/EventService.php";

class ApiController
{
    public $functionName;
    public $args = array();
    private $database;
    /**
     * @var UserService
     */
    private $adm;
    /**
     * @var TalkService
     */
    private $talk;
    /**
     * @var EventService
     */
    private $event;
    private $status;


    public function __construct($db_name, $db_user, $db_password)
    {
        $this->database = new DatabaseManager($db_name, $db_user, $db_password);
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
                $this->status = StatusHandler::not_implemented();
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
        $this->talk = new TalkService($this->database);
        $this->event = new EventService($this->database);
    }

    private function _open()
    {
        $this->database->loadDatabase();
        $this->status = StatusHandler::success();
    }

    private function _organizer()
    {
        if($this->adm->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");
    }

    private function _user()
    {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error("not admin");
            return;
        }

        if ($this->adm->registerUser($this->args['newlogin'], $this->args['newpassword']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");

    }

    private function _event()
    {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error("not admin");
            return;
        }

        if ($this->event->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");
    }

    private function _talk()
    {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error("not admin");
            return;
        }

        if (!$this->adm->userExists($this->args['speakerlogin'])) {
            $this->status = StatusHandler::error("speakerlogin nie istnieje");
            return;
        }

        if ($this->talk->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'], $this->args['room'], $this->args['initial_evaluation'], $this->args['eventname'])) {
            $this->database->evaluationTalk($this->adm->getUserLogin(), $this->args['talk'], $this->args['initial_evaluation'], true);
            $this->status = StatusHandler::success();
        }
        else
            $this->status = StatusHandler::error("sth went wrong");

    }

    private function _reject()
    {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error("not admin");
            return;
        }

        if ($this->talk->rejectTalk($this->args['talk']))
                $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");

    }

    private function _registerUserForEvent()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        if ($this->event->registerUserForEvent($this->adm->getUserLogin(), $this->args['eventname']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");
    }

    private function _attendance()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        if ($this->talk->checkAttendance($this->adm->getUserLogin(), $this->args['talk']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");
    }

    private function _evaluation()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        if ($this->talk->evaluationTalk($this->adm->getUserLogin(), $this->args['talk'], $this->args['rating']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");
    }

    private function _proposal()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        if ($this->talk->createProposalTalk($this->adm->getUserLogin(), $this->args['talk'], $this->args['title'], $this->args['start_timestamp']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");
    }

    private function _proposals()
    {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error("not admin");
            return;
        }

        $this->status = StatusHandler::success($this->talk->proposal());
    }

    private function _friends()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        if ($this->adm->addFriend($this->args['login2']))
            $this->status = StatusHandler::success();
        else
            $this->status = StatusHandler::error("sth went wrong");

    }

    private function _userPlan()
    {
        $this->status = StatusHandler::success($this->adm->getUserPlan($this->args['login'], $this->args['limit']));
    }

    private function _dayPlan()
    {
        $this->status = StatusHandler::success($this->talk->getDayPlan($this->args['timestamp']));
    }

    private function _bestTalks()
    {
        $this->status = StatusHandler::success($this->talk->getBestTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit'], $this->args['all']));
    }

    private function _mostPopularTalks()
    {
        $this->status = StatusHandler::success($this->talk->getMostPopularTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function _attendedTalks()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        $this->status = StatusHandler::success($this->talk->attendedTalks($this->adm->getUserLogin()));
    }

    private function _abandonedTalks()
    {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error("not admin");
            return;
        }

        $this->status = StatusHandler::success($this->talk->abandonedTalks($this->args['limit']));
    }

    private function _recentlyAddedTalks()
    {
        $this->status = StatusHandler::not_implemented();
    }

    private function _rejectedTalks()
    {
        if ($this->adm->isAdmin())
            $this->status = StatusHandler::success($this->talk->getAllRejectedTalks());
        elseif ($this->adm->isLogged())
            $this->status = StatusHandler::success($this->talk->getAllRejectedTalksForUser($this->adm->getUserLogin()));
        else
            $this->status = StatusHandler::error("sth went wrong");
    }

    private function _friendsTalks()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        $this->status = StatusHandler::success($this->talk->friendsTalks($this->adm->getUserLogin(), $this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function _friendsEvents()
    {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error("not logged");
            return;
        }

        $this->status = StatusHandler::success($this->adm->friendsEvents($this->args['event']));
    }



}