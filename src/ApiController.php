<?php
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
    private $userService;
    /**
     * @var TalkService
     */
    private $talkService;
    /**
     * @var EventService
     */
    private $eventService;
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
        $this->prepareArguments();
        $this->init();
        $this->run();
        $this->printResult();
    }

    private function prepareArguments() {
        /**
         * Najlepiej przed SQLInjection użyć tutaj PDO
         * <ale uznałem, że może być problem z instalacją>
         */
        foreach ($this->args as $key => $value) {
            $this->args[$key] = str_replace( ";", "", $value);
        }
    }

    private function init() {
        /**
         * Nie fajnie, że jedna komenda w API inny argument loginu :c
         */
        
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

        $this->userService = new UserService($userLogin, $userPassword, $this->database);
        $this->talkService = new TalkService($this->database);
        $this->eventService = new EventService($this->database);
    }

    private function run() {
        call_user_func(array($this, $this->functionName));
    }

    private function open() {
        $this->database->loadDatabase();
        $this->status = StatusHandler::success();
    }

    private function organizer() {
        if($this->userService->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function user() {
        if (!$this->userService->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        if ($this->userService->registerUser($this->args['newlogin'], $this->args['newpassword'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function event() {
        if (!$this->userService->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        if ($this->eventService->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function talk() {
        if (!$this->userService->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN );
            return;
        }

        if (!$this->userService->userExists($this->args['speakerlogin'])) {
            $this->status = StatusHandler::error(StatusHandler::USER_DOES_NOT_EXIST);
            return;
        }

        if ($this->talkService->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'], $this->args['room'], $this->args['eventname'])) {
            $this->database->evaluationTalk($this->userService->getUserLogin(), $this->args['talk'], $this->args['initial_evaluation'], true);
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }

    }

    private function reject() {
        if (!$this->userService->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        if ($this->talkService->rejectTalk($this->args['talk'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }

    }

    private function register_user_for_event() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->eventService->registerUserForEvent($this->userService->getUserLogin(), $this->args['eventname'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function attendance() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->talkService->checkAttendance($this->userService->getUserLogin(), $this->args['talk'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function evaluation() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->talkService->evaluationTalk($this->userService->getUserLogin(), $this->args['talk'], $this->args['rating'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function proposal() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->talkService->createProposalTalk($this->userService->getUserLogin(), $this->args['talk'], $this->args['title'], $this->args['start_timestamp'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }
    }

    private function proposals() {
        if (!$this->userService->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        $this->status = StatusHandler::success($this->talkService->proposal());
    }

    private function friends() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        if ($this->userService->addFriend($this->args['login2'])) {
            $this->status = StatusHandler::success();
        }
        else {
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
        }

    }

    private function user_plan() {
        $this->status = StatusHandler::success($this->userService->getUserPlan($this->args['login'], $this->args['limit']));
    }

    private function day_plan() {
        $this->status = StatusHandler::success($this->talkService->getDayPlan($this->args['timestamp']));
    }

    private function best_talks() {
        $this->status = StatusHandler::success($this->talkService->getBestTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit'], $this->args['all']));
    }

    private function most_popular_talks() {
        $this->status = StatusHandler::success($this->talkService->getMostPopularTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function attended_talks() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->talkService->attendedTalks($this->userService->getUserLogin()));
    }

    private function abandoned_talks() {
        if (!$this->userService->isAdmin()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_ADMIN);
            return;
        }

        $this->status = StatusHandler::success($this->talkService->abandonedTalks($this->args['limit']));
    }

    private function recently_added_talks() {
        $this->status = StatusHandler::not_implemented();
    }

    private function rejected_talks() {
        if ($this->userService->isAdmin())
            $this->status = StatusHandler::success($this->talkService->getAllRejectedTalks());
        elseif ($this->userService->isLogged())
            $this->status = StatusHandler::success($this->talkService->getAllRejectedTalksForUser($this->userService->getUserLogin()));
        else
            $this->status = StatusHandler::error(StatusHandler::STH_WRONG);
    }

    private function friends_talks() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->talkService->friendsTalks($this->userService->getUserLogin(), $this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function friends_events() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->userService->friendsEvents($this->args['eventname']));
    }

    private function recommended_talks() {
        if (!$this->userService->isLogged()) {
            $this->status = StatusHandler::error(StatusHandler::NOT_LOGGED);
            return;
        }

        $this->status = StatusHandler::success($this->talkService->recommendedTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']));
    }

    private function printResult() {
        echo json_encode($this->status);
    }


}