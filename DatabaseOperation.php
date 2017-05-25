<?php
interface DatabaseOperation
{
    public function userExists($user_login, $user_password);
    public function isAdmin($user_login, $user_password);
    public function registerUser($user_login, $user_password);
    public function registerOrganizer($user_login, $user_password);
    public function isLoginBusy($user_login);
    public function isEventNameBusy($event_name);
    public function existsProposalTalk($talk_id);
    public function existsTalk($talk_id);
    public function createEvent($event_name, $start_timestamp, $end_timestamp);
    public function createTalk($user_login, $talk, $title, $start_timestamp, $room, $event_name = NULL);
    public function createProposalTalk($user_login, $talk, $title, $start_timestamp);
    public function registerUserForEvent($user_login, $event_name);
    public function checkAttendance($user_login, $talk_id);
    public function evaluationTalk($user_login, $talk_id, $rate);
    public function rejectTalk($talk_id);
    public function proposalTalk($user_login, $talk_id, $title, $start_timestamp);
    public function addFriend($user_login, $user_login2);
    public function getStartTimeStampOfEvent($event_name);
    public function getUserPlan($user_login, $limit);
    public function getDayPlan($timestamp);
    public function getBestTalks($start_timestamp, $end_timestamp, $limit, $all);
    public function getMostPopularTalks($start_timestamp, $end_timestamp, $limit);

}