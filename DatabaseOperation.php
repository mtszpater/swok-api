<?php

/**
 * Created by PhpStorm.
 * User: pater
 * Date: 22.05.2017
 * Time: 23:42
 */
interface DatabaseOperation
{

    public function userExists($user_login, $user_password);
    public function isAdmin($user_login, $user_password);
    public function registerUser($user_login, $user_password);
    public function isLoginBusy($user_login);
    public function createEvent($event_name, $start_timestamp, $end_timestamp);
//<login> <password> <speakerlogin> <talk> <title> <start_timestamp> <room> <initial_evaluation> <eventname>
//    public function createTalk($user_login, $talk_id);

}