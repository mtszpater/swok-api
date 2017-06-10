<?php
require_once "DatabaseManager.php";
require_once "service/UserService.php";
require_once "service/TalkService.php";
require_once "service/EventService.php";

/**
 * Class ApiController
 */
class ApiController
{
    /**
     * @var
     */
    public $functionName;
    /**
     * @var array
     */
    public $args = array();
    /**
     * @var DatabaseManager
     */
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
    /**
     * @var
     */
    private $status;


    /**
     * ApiController constructor.
     * @param $dbName
     * @param $dbUser
     * @param $dbPassword
     */
    public function __construct($dbName, $dbUser, $dbPassword) {
        $this->database = new DatabaseManager($dbName, $dbUser, $dbPassword);
    }

    public function execute() {
        $this->_prepareArguments();
        $this->_init();
        $this->_run();
        $this->_printResult();
    }

    private function _prepareArguments() {
        /**
         * Najlepiej przed SQLInjection użyć tutaj PDO
         * <ale uznałem, że może być problem z instalacją>
         */
        foreach ($this->args as $key => $value) {
            $this->args[$key] = str_replace( ";", "", $value);
        }
    }

    private function _init() {
        $userPassword = '';
        $userLogin = '';

        if(isset($this->args['password'])) {
            $userPassword = $this->args['password'];
        }

        if(isset($this->args['login'])) {
            $userLogin = $this->args['login'];
        }
        
        if(isset($this->args['login1'])) {
            $userLogin = $this->args['login1'];
        }

        /**
         * Nie fajnie, że jedna komenda w API inny argument loginu :c
         */
        $this->adm = new UserService($userLogin, $userPassword, $this->database);
        $this->talk = new TalkService($this->database);
        $this->event = new EventService($this->database);
    }

    private function _run() {
        switch ($this->functionName) {
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
                $this->_recommendedTalks();
                break;

            default:
                break;

        }
    }

    private function _open() {
        $this->database->loadDatabase();
        $this->status = StatusHandler::success();
    }

    private function _organizer() {
        if($this->adm->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function _user() {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        if ($this->adm->registerUser($this->args['newlogin'], $this->args['newpassword'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function _event() {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        if ($this->event->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function _talk() {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN );
            return;
        }

        if (!$this->adm->userExists($this->args['speakerlogin'])) {
            $this->status = StatusHandler::error(StatusHandler::USER_DOES_NOT_EXIST);
            return;
        }

        if ($this->talk->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'], $this->args['room'], $this->args['eventname'])) {
            $this->database->evaluationTalk($this->adm->getUserLogin(), $this->args['talk'], $this->args['initial_evaluation'], true);
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }

    }

    private function _reject() {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        if ($this->talk->rejectTalk($this->args['talk'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }

    }

    private function _registerUserForEvent() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->event->registerUserForEvent($this->adm->getUserLogin(), $this->args['eventname'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function _attendance() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->talk->checkAttendance($this->adm->getUserLogin(), $this->args['talk'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function _evaluation() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->talk->evaluationTalk($this->adm->getUserLogin(), $this->args['talk'], $this->args['rating'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function _proposal() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->talk->createProposalTalk($this->adm->getUserLogin(), $this->args['talk'], $this->args['title'], $this->args['start_timestamp'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function _proposals() {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        $this->status = StatusHandler::success($this->talk->proposal());
    }

    private function _friends() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->adm->addFriend($this->args['login2'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }

    }

    private function _userPlan() {
        $this->status = StatusHandler::success($this->adm->getUserPlan($this->args['login'], $this->args['limit']));
    }

    private function _dayPlan() {
        $this->status = StatusHandler::success($this->talk->getDayPlan($this->args['timestamp']));
    }

    private function _bestTalks() {
        $this->status = StatusHandler::success($this->talk->getBestTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit'], $this->args['all']));
    }

    private function _mostPopularTalks() {
        $this->status = StatusHandler::success($this->talk->getMostPopularTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function _attendedTalks() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->talk->attendedTalks($this->adm->getUserLogin()));
    }

    private function _abandonedTalks() {
        if (!$this->adm->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        $this->status = StatusHandler::success($this->talk->abandonedTalks($this->args['limit']));
    }

    private function _recentlyAddedTalks() {
        $this->status = StatusHandler::not_implemented();
    }

    private function _rejectedTalks() {
        if ($this->adm->isAdmin())
            $this->status = StatusHandler::success($this->talk->getAllRejectedTalks());
        elseif ($this->adm->isLogged())
            $this->status = StatusHandler::success($this->talk->getAllRejectedTalksForUser($this->adm->getUserLogin()));
        else
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
    }

    private function _friendsTalks() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->talk->friendsTalks($this->adm->getUserLogin(), $this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function _friendsEvents() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->adm->friendsEvents($this->args['eventname']));
    }

    private function _recommendedTalks() {
        if (!$this->adm->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->talk->recommendedTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function _printResult() {
        echo json_encode($this->status);
    }


}