<?php
include_once "DatabaseOperation.php";

class DatabaseManager implements DatabaseOperation {
	private $connection = null;

	public function __construct($host = 'localhost', $port = '5432', $dbname = 'springbootdb', $user = 'ja', $password = '' ){
		$this->connection = pg_connect("host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."");
	}

    public function userExists($user_login, $user_password){
		$query = pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password';");

		return pg_num_rows($query) == 1 ? true : false;
	}

	public function isAdmin($user_login, $user_password){
        $query = pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password' AND admin='1';");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function createEvent($event_name, $start_timestamp, $end_timestamp){
        return pg_query($this->connection, "insert into event (name, date_start, date_end) VALUES ('$event_name', '$start_timestamp', '$end_timestamp');") ? true : false;
    }

	public function registerUser($user_login, $user_password){
        return pg_query($this->connection, "insert into member (login, password) VALUES ('$user_login', '$user_password')") ? true : false;
    }

    public function isLoginBusy($user_login){
        $query = pg_query($this->connection, "select * from member WHERE login='$user_login'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function createTalk($user_login, $talk, $title, $start_timestamp, $room, $initial_evaluation, $event_name = NULL)
    {
        return pg_query($this->connection, "insert into talk (id, title, login, date_start, room, event_name) VALUES('$talk', '$title', '$user_login', '$start_timestamp', '$room', '$event_name')") ? true : false;
        // TODO: ocenianie organizatora
    }

    public function registerUserForEvent($user_login, $event_name)
    {
        return pg_query($this->connection, "insert into registrations_on_events (login, event_name) VALUES ('$user_login', '$event_name')") ? true : false;
    }

    public function checkAttendance($user_login, $talk_id)
    {
        return pg_query($this->connection, "insert into attendance_on_talks (login, talk_id) VALUES ('$user_login', '$talk_id')") ? true : false;
    }

    public function evaluationTalk($user_login, $talk_id, $rate)
    {
        return pg_query($this->connection, "insert into rate (talk_id, login, rate) VALUES ('$talk_id', '$user_login', '$rate')") ? true : false;
    }

    public function rejectTalk($talk_id)
    {
        // TODO: Implement rejectTalk() method.
    }

    public function proposalTalk($user_login, $talk_id, $title, $start_timestamp)
    {
        return pg_query($this->connection, "insert into talk_proposal (id, title, login, date_start) VALUES ('$talk_id', '$title', '$user_login', '$start_timestamp')") ? true : false;
    }

    public function registerOrganizer($user_login, $user_password)
    {
        return pg_query($this->connection, "insert into member (login, password, admin) VALUES ('$user_login', '$user_password', true)") ? true : false;
    }
}
?>