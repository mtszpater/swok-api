<?php
/**
 * Class TalkService
 */
class TalkService
{
    /**
     * @var DatabaseManagerInterface
     */
    private $database;

    /**
     * TalkService constructor.
     * @param DatabaseManagerInterface $database
     */
    public function __construct(DatabaseManagerInterface $database) {
        $this->database = $database;
    }

    /**
     * @param $userLogin
     * @param $talkId
     * @param $title
     * @param $startTimestamp
     * @param $room
     * @param $eventName
     * @return bool
     */
    public function createTalk($userLogin, $talkId, $title, $startTimestamp, $room, $eventName) {
        if( ! ($eventName === "") ) {
            if (!$this->database->isEventNameBusy($eventName)) {
                return false;
            }

            if ($this->database->existsTalk($talkId)) {
                return false;
            }

            $event_start = $this->database->getStartTimeStampOfEvent($eventName);

            if ($event_start >= $startTimestamp) {
                return false;
            }

        }
        else {
            $eventName = NULL;
        }

        if ($this->database->createTalk($userLogin, $talkId, $title, $startTimestamp, intval($room), $eventName)) {

            if ($this->database->existsProposalTalk($talkId)) {
                $this->deleteTalk($talkId);
            }
            
            return true;
        }

        return false;
    }

    /**
     * @param $talkId
     * @return bool
     */
    public function rejectTalk($talkId) {
        if (!$this->database->existsProposalTalk($talkId)) {
            return false;
        }

        if ($this->database->rejectTalk($talkId)) {
            return true;
        }

        return false;
    }

    /**
     * @param $talkId
     * @return bool
     */
    public function deleteTalk($talkId) {
        if (!$this->database->existsProposalTalk($talkId)) {
            return false;
        }

        if ($this->database->deleteProposalTalk($talkId)) {
            return true;
        }

        return false;
    }

    /**
     * @param $userLogin
     * @param $talkId
     * @return bool
     */
    public function checkAttendance($userLogin, $talkId) {
        if ($this->database->checkAttendance($userLogin, $talkId)) {
            return true;
        }

        return false;
    }

    /**
     * @param $userLogin
     * @param $talkId
     * @param $rate
     * @return bool
     */
    public function evaluationTalk($userLogin, $talkId, $rate) {
        if (!$this->database->existsTalk($talkId)) {
            return false;
        }

        if ($this->database->evaluationTalk($userLogin, $talkId, intval($rate))) {
            return true;
        }

        return false;
    }

    /**
     * @param $userLogin
     * @param $talkId
     * @param $title
     * @param $startTimestamp
     * @return bool
     */
    public function createProposalTalk($userLogin, $talkId, $title, $startTimestamp) {
        if ($this->database->existsProposalTalk($talkId)) {
            return false;
        }

        if ($this->database->existsTalk($talkId)) {
            return false;
        }

        if ($this->database->createProposalTalk($userLogin, $talkId, $title, $startTimestamp)) {
            return true;
        }

        return false;
    }

    /**
     * @param $userLogin
     * @return mixed
     */
    public function attendedTalks($userLogin) {
        return $this->database->getAttendedTalks($userLogin);
    }

    /**
     * @param $limit
     * @return mixed
     */
    public function abandonedTalks($limit) {
        return $this->database->getAbandonedTalks($limit);
    }

    /**
     * @return mixed
     */
    public function getAllRejectedTalks() {
        return $this->database->getAllRejectedTalks();
    }

    /**
     * @param $userLogin
     * @return mixed
     */
    public function getAllRejectedTalksForUser($userLogin) {
        return $this->database->getAllRejectedTalksForUser($userLogin);
    }

    /**
     * @return mixed
     */
    public function proposal() {
        return $this->database->getProposalTalks();
    }

    /**
     * @param $userLogin
     * @param $startTimestamp
     * @param $endTimestamp
     * @param $limit
     * @return mixed
     */
    public function friendsTalks($userLogin, $startTimestamp, $endTimestamp, $limit) {
        return $this->database->getFriendsTalks($userLogin, $startTimestamp, $endTimestamp, $limit);
    }

    /**
     * @param $timestamp
     * @return mixed
     */
    public function getDayPlan($timestamp) {
        return $this->database->getDayPlan($timestamp);
    }

    /**
     * @param $startTimestamp
     * @param $endTimestamp
     * @param $limit
     * @param $all
     * @return mixed
     */
    public function getBestTalks($startTimestamp, $endTimestamp, $limit, $all) {
        return $this->database->getBestTalks($startTimestamp, $endTimestamp, $limit, $all);
    }

    /**
     * @param $startTimestamp
     * @param $endTimestamp
     * @param $limit
     * @return mixed
     */
    public function getMostPopularTalks($startTimestamp, $endTimestamp, $limit) {
        return $this->database->getMostPopularTalks($startTimestamp, $endTimestamp, $limit);
    }

    /**
     * @param $limit
     * @return bool
     */
    public function getRecentlyAddedTalks($limit) {
        return false;
    }

    /**
     * @param $startTimestamp
     * @param $endTimestamp
     * @param $limit
     * @return mixed
     */
    public function recommendedTalks($startTimestamp, $endTimestamp, $limit) {
        return $this->database->getMostPopularTalks($startTimestamp, $endTimestamp, $limit);
    }

}