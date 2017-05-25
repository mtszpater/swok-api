<?php
require_once "DatabaseManager.php";
require_once "UserService.php";

class OperationManager
{
    public $functionName;
    public $args = array();
    private $database;
    /**
     * @var UserService
     */
    private $adm;
    private $status;


    public function __construct()
    {
        $this->database = new DatabaseManager();
    }

    public function execute(){
        $this->_loginUser();

        switch($this->functionName){
            case "open":
                $this->database = new DatabaseManager(null, null, $this->args['baza'], $this->args['login'], $this->args['password']);
                $this->status = array('status' => 'OK');
                break;

            case "organizer":
                $this->status = $this->adm->createOrganizer($this->args['newlogin'], $this->args['newpassword'], $this->args['secret']);
                break;

            case "user":
                $this->status = $this->adm->registerUser($this->args['newlogin'], $this->args['newpassword']);
                break;

            case "event":
                $this->status = $this->adm->createEvent($this->args['eventname'], $this->args['start_timestamp'], $this->args['end_timestamp']);
                break;

            case "talk":
                // TODO:
//        if($this->args['eventname'] === ' ')
//            $this->args['eventname'] = NULL;
                $this->status = $this->adm->createTalk($this->args['speakerlogin'], $this->args['talk'], $this->args['title'], $this->args['start_timestamp'], $this->args['room'], $this->args['initial_evaluation'], $this->args['eventname']);
                break;

            case "reject":
                $this->status = $this->adm->rejectTalk($this->args['talk']);
                break;

            case "register_user_for_event":
                $this->status = $this->adm->registerUserForEvent($this->args['eventname']);
                break;

            case "attendance":
                $this->status = $this->adm->checkAttendance($this->args['talk']);
                break;

            case "evaluation":
                $this->status = $this->adm->evaluationTalk($this->args['talk'], $this->args['rating']);
                break;

            case "proposal":
                // metoda "proposal" działa na dwa sposoby :c
                if(isset($this->args['talk']))
                    $this->status = $this->adm->createProposalTalk($this->args['talk'], $this->args['title'], $this->args['start_timestamp']);
                else
                    $this->status = $this->adm->proposal();
                break;

            case "friends":
                $this->status = $this->adm->addFriend($this->args['login2']);
                break;

            case "user_plan":
                $this->status = $this->adm->getUserPlan($this->args['login'], $this->args['limit']);
                break;

            case "day_plan":
                $this->status = $this->adm->getDayPlan($this->args['timestamp']);
                break;

            case "best_talks":
                $this->status = $this->adm->getBestTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit'], $this->args['all']);
                break;

            case "most_popular_talks":
                $this->status = $this->adm->getMostPopularTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']);
                break;

            case "attended_talks":
                $this->status = $this->adm->attendedTalks();
                break;

            case "abandoned_talks":
                $this->status = $this->adm->abandonedTalks($this->args['limit']);
                break;

            case "recently_added_talks":
                $this->status = $this->adm->getRecentlAddedTalks($this->args['limit']);
                break;

            case "rejected_talks":
                $this->status = $this->adm->rejectedTalks();
                break;

            case "friends_talks":
                $this->status = $this->adm->friendsTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']);
                break;

            case "friends_events";
                $this->status = $this->adm->friendsEvents($this->args['event']);
                break;

            case "recommended_talks";
                $this->status = $this->adm->recommendedTalks($this->args['start_timestamp'], $this->args['end_timestamp'], $this->args['limit']);
                break;

        }
        echo json_encode($this->status);
    }

    private function _loginUser()
    {
        $this->adm = new UserService($this->database);
        if(isset($this->args['password'])) $this->adm->setUserPassword($this->args['password']);
        if(isset($this->args['login'])) $this->adm->setUserLogin($this->args['login']);

        /* Nie fajnie, że jedna komenda w API inny argument loginu :c */
        if(isset($this->args['login1'])) $this->adm->setUserLogin($this->args['login1']);
    }
}