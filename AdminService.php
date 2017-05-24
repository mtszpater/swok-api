<?php
include_once "DatabaseOperation.php";

class UserService
{

    private $user_login;
    private $user_password;
    private $database;
// TODO: sql injection
    public function __construct($user_login = '', $user_password = '', DatabaseOperation $database){
        $this->database = $database;
        $this->user_login = $user_login;
        $this->user_password = $user_password;
    }

    public function createTalk($user_login, $talk_id, $title, $start_timestamp, $room, $initial_evaluation, $event_name){
        if(! $this->database->isAdmin($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if(! $this->database->isLoginBusy($user_login)) return $this->_error("User doesnt exist");
        if(! $this->database->isEventNameBusy($event_name)) return $this->_error("event doesnt exist: $event_name");
        if($this->database->existsTalk($talk_id)) return $this->_error("busy talk_id: $talk_id");

        $event_start = $this->database->getStartTimeStampOfEvent($event_name);
        if( $event_start >= $start_timestamp) return $this->_error("bad timestamp: $start_timestamp ");

        if($this->database->createTalk($user_login, intval($talk_id), $title, $start_timestamp, intval($room), $event_name)) {

            $this->evaluationTalk($talk_id, $initial_evaluation);

            if( $this->database->existsProposalTalk($talk_id)){
                $this->rejectTalk($talk_id);
            }

            return $this->_success();
        }

        return $this->_error("sth went wrong");
    }

    public function rejectTalk($talk_id){
        if(! $this->database->isAdmin($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if(! $this->database->existsProposalTalk($talk_id)) return $this->_error("didnt found talk_id: $talk_id");

        if($this->database->rejectTalk($talk_id))
            return $this->_success();

        return $this->_error("sth went wrong");
    }

    public function registerUser($user_login, $user_password){
        if (! $this->database->isAdmin($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if($this->database->isLoginBusy($user_login)) return $this->_error("busy login: $user_login");

        if($this->database->registerUser($user_login, $user_password))
            return $this->_success();

        return $this->_error("sth went wrong");
    }

    public function createEvent($event_name, $start, $end){
        if (! $this->database->isAdmin($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if($this->database->isEventNameBusy($event_name)) return $this->_error("busy eventname: $event_name");

        if($this->database->createEvent($event_name, $start, $end))
            return $this->_success();

        return $this->_error("sth went wrong");
    }

    public function createOrganizer($user_login, $user_password, $secret){
        if(! ($secret === "d8578edf8458ce06fbc5bb76a58c5ca4")) return $this->_error("Wrong secret key");
        if($this->database->isLoginBusy($user_login)) return $this->_error("busy login: $user_login");

        if($this->database->registerOrganizer($user_login, $user_password))
            return $this->_success();

        return $this->_error("sth went wrong");
    }

    public function registerUserForEvent($event_name){
        if(! $this->database->userExists($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if(! $this->database->isEventNameBusy($event_name)) return $this->_error("event doesnt exist: $event_name");

        if($this->database->registerUserForEvent($this->user_login, $event_name))
            return $this->_success();

        return $this->_error("sth went wrong");
    }

    public function checkAttendance($talk_id){
        if(! $this->database->userExists($this->user_login, $this->user_password)) return $this->_error("Permission denied");

        if($this->database->checkAttendance($this->user_login, $talk_id))
            return $this->_success();

        return $this->_error("sth went wrong");
    }

    public function evaluationTalk($talk_id, $rate){
        if(! $this->database->userExists($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if(! $this->database->existsTalk($talk_id)) return $this->_error("talk doesnt exist: $talk_id");

        if($this->database->evaluationTalk($this->user_login, $talk_id, $rate))
            return $this->_success();

        return $this->_error("sth went wrong");
    }

    public function createProposalTalk($talk_id, $title, $start_timestamp){
        if(! $this->database->userExists($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if($this->database->existsProposalTalk($talk_id)) return $this->_error("busy proposal_talk_id: $talk_id");
        if($this->database->existsTalk($talk_id)) return $this->_error("busy talk_id ( in column talk ): $talk_id");

        if($this->database->createProposalTalk($this->user_login, intval($talk_id), $title, $start_timestamp)) {
            return $this->_success();
        }

        return $this->_error("sth went wrong");
    }


    public function addFriend($user_login){
        if(! $this->database->userExists($this->user_login, $this->user_password)) return $this->_error("Permission denied");
        if($user_login === $this->user_login) return $this->_error("Im my friend :D");
        if(! $this->database->isLoginBusy($user_login)) return $this->_error("your friend doesnt exist : $user_login");

        if($this->database->addFriend($this->user_login, $user_login)) {
            return $this->_success();
        }

        return $this->_error("sth went wrong");
    }

    public function getUserPlan($limit){
        if(! $this->database->isLoginBusy($this->user_login)) return $this->_error("user doesnt exist : $this->user_login");

        return $this->_success(true, $this->database->getUserPlan($this->user_login, $limit));
    }

    public function getDayPlan($timestamp){
        return $this->_success(true, $this->database->getDayPlan($timestamp));
    }

    public function getBestTalks($start_timestamp, $end_timestamp, $limit, $all){
        return $this->_success(true, $this->database->getBestTalks($start_timestamp, $end_timestamp, $limit, $all));
    }

    public function getMostPopularTalks($start_timestamp, $end_timestamp, $limit){
        return $this->_success(true, $this->database->getMostPopularTalks($start_timestamp, $end_timestamp, $limit));
    }

    private function _error($msg){
        return array('status' => 'ERROR', 'msg' => $msg);
    }

    private function _success($with_data = false, $msg = array()){
        if(!$msg) $msg = array();

        if($with_data)
            return array('status' => 'OK', 'data' => $msg);
        else
            return array('status' => 'OK');
    }
}